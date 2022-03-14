<?php

namespace Meetanshi\Partialpro\Model\Api;

use Magento\Payment\Model\Method\Logger;
use Magento\Customer\Helper\Address;
use Psr\Log\LoggerInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Paypal\Model\Api\ProcessableExceptionFactory;
use Magento\Framework\Exception\LocalizedExceptionFactory;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\QuoteFactory;
use Meetanshi\Partialpro\Helper\Data as partialHelper;

class Nvp extends \Magento\Paypal\Model\Api\Nvp
{
    protected $quoteFactory;
    protected $_api;
    public $cart;
    public $partialHelper;

    public function __construct(
        Address $customerAddress,
        LoggerInterface $logger,
        Logger $customLogger,
        ResolverInterface $localeResolver,
        RegionFactory $regionFactory,
        CountryFactory $countryFactory,
        ProcessableExceptionFactory $processableExceptionFactory,
        LocalizedExceptionFactory $frameworkExceptionFactory,
        CurlFactory $curlFactory,
        QuoteFactory $quoteFactory,
        Cart $cart,
        UrlInterface $urlInterface,
        partialHelper $partialHelper,
        array $data = []
    )
    {

        parent::__construct($customerAddress, $logger, $customLogger, $localeResolver, $regionFactory, $countryFactory, $processableExceptionFactory, $frameworkExceptionFactory, $curlFactory, $data);
        $this->_countryFactory = $countryFactory;
        $this->_processableExceptionFactory = $processableExceptionFactory;
        $this->_frameworkExceptionFactory = $frameworkExceptionFactory;
        $this->_curlFactory = $curlFactory;
        $this->_urlInterface = $urlInterface;
        $this->quoteFactory = $quoteFactory;
        $this->cart = $cart;
        $this->partialHelper = $partialHelper;
    }

    public function callSetExpressCheckout()
    {
        $this->_prepareExpressCheckoutCallRequest($this->_setExpressCheckoutRequest);
        $request = $this->_exportToRequest($this->_setExpressCheckoutRequest);
        $this->_exportLineItems($request);

        $quoteId = $this->cart->getQuote()->getId();
        $quote = $this->quoteFactory->create()->load($quoteId);

        $options = $this->getShippingOptions();
        if ($this->getAddress()) {
            $request = $this->_importAddresses($request);
            $request['ADDROVERRIDE'] = 1;
        } elseif ($options) {
            $request['CALLBACK'] = $this->getShippingOptionsCallbackUrl();
            $request['CALLBACKTIMEOUT'] = 6;
            $request['MAXAMT'] = $request['AMT'] + 999.00;
            $this->_exportShippingOptions($request);
        }
        if ($quote->getPartialPayNow() > 0) {

            $amount = $quote->getPartialPayNow();
            $amount = $this->partialHelper->convertCurrency($quote->getBaseCurrencyCode(),$quote->getQuoteCurrencyCode(),$amount);

            $request['AMT'] = number_format((float)$amount, 2, '.', '');
            $request['ITEMAMT'] = number_format((float)$amount, 2, '.', '');
            $request['TAXAMT'] = 0;
            $request['SHIPPINGAMT'] = 0;
        }
        $response = $this->call(self::SET_EXPRESS_CHECKOUT, $request);
        $this->_importFromResponse($this->_setExpressCheckoutResponse, $response);
    }


    public function callDoExpressCheckoutPayment()
    {
        $quoteId = $this->cart->getQuote()->getId();
        $quote = $this->quoteFactory->create()->load($quoteId);

        $this->_prepareExpressCheckoutCallRequest($this->_doExpressCheckoutPaymentRequest);
        $request = $this->_exportToRequest($this->_doExpressCheckoutPaymentRequest);
        $this->_exportLineItems($request);

        if ($this->getAddress()) {
            $request = $this->_importAddresses($request);
            $request['ADDROVERRIDE'] = 1;
        }
        if ($quote->getPartialPayNow() > 0) {

            $amount = $quote->getPartialPayNow();
            $amount = $this->partialHelper->convertCurrency($quote->getBaseCurrencyCode(),$quote->getQuoteCurrencyCode(),$amount);

            $request['AMT'] = number_format((float)$amount, 2, '.', '');
            $request['ITEMAMT'] = number_format((float)$amount, 2, '.', '');
            $request['TAXAMT'] = 0;
            $request['SHIPPINGAMT'] = 0;
        }
        $response = $this->call(self::DO_EXPRESS_CHECKOUT_PAYMENT, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doExpressCheckoutPaymentResponse, $response);
        $this->_importFromResponse($this->_createBillingAgreementResponse, $response);
    }

    public function callDoCapture()
    {
        $this->setCompleteType($this->_getCaptureCompleteType());
        $request = $this->_exportToRequest($this->_doCaptureRequest);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->loadByIncrementId($request['INVNUM']);

        $request['AMT'] = number_format((float)$order->getPartialPayNow(), 2, '.', '');

        $response = $this->call(self::DO_CAPTURE, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doCaptureResponse, $response);
    }

    public function callInstallmentSetExpressCheckout($order, $payment)
    {
        try {
            $this->_config = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Paypal\Model\Config::class);
            $this->_config->setMethod("paypal_express");
            $solutionType = $this->_config->getMerchantCountry() == 'IN'
                ? \Magento\Paypal\Model\Config::EC_SOLUTION_TYPE_MARK
                : $this->_config->getValue('solutionType');

            $this->_api = $this->setConfigObject($this->_config);
            $this->_api->setAmount($payment->getAmount())
                ->setCurrencyCode($order->getBaseCurrencyCode())
                ->setInvNum($payment->getIncrementId())
                ->setReturnUrl($this->_urlInterface->getUrl('partialpayment/paypal/return'))
                ->setCancelUrl($this->_urlInterface->getUrl('partialpayment/paypal/cancel'))
                ->setSolutionType($solutionType)
                ->setPaymentAction($this->_config->getValue('paymentAction'))
                ->setBillingAddress($order->getBillingAddress())
                ->setAddress($order->getShippingAddress())
                ->setBillingAddress($order->getBillingAddress());
            $this->_api->addData(
                [
                    'giropay_cancel_url' => $this->_urlInterface->getUrl('partialpayment/paypal/cancel'),
                    'giropay_success_url' => $this->_urlInterface->getUrl('partialpayment/paypal/success'),
                    'giropay_bank_txn_pending_url' => $this->_urlInterface->getUrl('partialpayment/paypal/success'),
                ]
            );
            $this->_prepareExpressCheckoutCallRequest($this->_setExpressCheckoutRequest);
            $request = $this->_exportToRequest($this->_setExpressCheckoutRequest);
            $this->_exportLineItems($request);
            $request['ITEMAMT'] = $payment->getAmount();
            $request['TAXAMT'] = 0;
            $request['SHIPPINGAMT'] = 0;
            $request = $this->_importAddresses($request);
            $request['ADDROVERRIDE'] = 1;

            $response = $this->call(self::SET_EXPRESS_CHECKOUT, $request);
            return $response;
        } catch (\Exception $e) {

            $response = [];
            $response['ACK'] = 'fail';
            $response['MESSAGE'] = $e->getMessage();
            return $response;
        }
    }

    public function callInstallmentGetExpressCheckout($token)
    {

        $this->_config = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Paypal\Model\Config::class);
        $this->_config->setMethod("paypal_express");
        $this->_api = $this->setConfigObject($this->_config);
        $this->_api->setToken($token);
        $this->_prepareExpressCheckoutCallRequest($this->_getExpressCheckoutDetailsRequest);
        $request = $this->_exportToRequest($this->_getExpressCheckoutDetailsRequest);
        $response = $this->call(self::GET_EXPRESS_CHECKOUT_DETAILS, $request);
        return $response;
    }

    public function callInstallmentDoExpressCheckoutPayment($token, $payerID, $invNum, $amount, $order)
    {
        $this->_config = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Paypal\Model\Config::class);
        $this->_config->setMethod("paypal_express");
        $this->_api = $this->setConfigObject($this->_config);
        $this->_api->setAmount($amount)
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setInvNum($invNum)
            ->setPaymentAction($this->_config->getValue('paymentAction'))
            ->setNotifyUrl($this->_urlInterface->getUrl('partialpro/paypal/ipn'))
            ->setBillingAddress($order->getBillingAddress())
            ->setAddress($order->getShippingAddress())
            ->setBillingAddress($order->getBillingAddress());

        $this->_prepareExpressCheckoutCallRequest($this->_doExpressCheckoutPaymentRequest);
        $request = $this->_exportToRequest($this->_doExpressCheckoutPaymentRequest);
        $this->_exportLineItems($request);
        $request['TOKEN'] = $token;
        $request['PAYERID'] = $payerID;
        $request['ITEMAMT'] = number_format((float)$amount, 2, '.', '');;
        $request['TAXAMT'] = 0;
        $request['SHIPPINGAMT'] = 0;

        $request = $this->_importAddresses($request);
        $request['ADDROVERRIDE'] = 1;

        $response = $this->call(self::DO_EXPRESS_CHECKOUT_PAYMENT, $request);
        return $response;
    }

    public function callInstallmentCapture($amount, $referenceId, $invId, $currenyCode)
    {
        $this->_config = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Paypal\Model\Config::class);
        $this->_config->setMethod("paypal_express");

        $this->_api = $this->setConfigObject($this->_config);
        $this->_api->setReferenceId($referenceId)
            ->setInvNum($invId)
            ->setPaymentAction($this->_config->getValue('paymentAction'))
            ->setAmount($amount)
            ->setNotifyUrl($this->_urlInterface->getUrl('partialpro/paypal/ipn'))
            ->setCurrencyCode($currenyCode);
        $this->_prepareExpressCheckoutCallRequest($this->_doReferenceTransactionRequest);
        $request = $this->_exportToRequest($this->_doReferenceTransactionRequest);
        $this->_exportLineItems($request);
        $request['ITEMAMT'] = 0;
        $request['SHIPPINGAMT'] = 0;
        $request['TAXAMT'] = 0;
        $response = $this->call('DoReferenceTransaction', $request);
        return $response;
    }

    public function callDoReferenceTransaction()
    {
        $request = $this->_exportToRequest($this->_doReferenceTransactionRequest);
        $this->_exportLineItems($request);

        $quoteId = $this->cart->getQuote()->getId();
        $quote = $this->quoteFactory->create()->load($quoteId);
        if ($quote->getPartialPayNow() > 0) {

            $amount = $quote->getPartialPayNow();
            $amount = $this->partialHelper->convertCurrency($quote->getBaseCurrencyCode(),$quote->getQuoteCurrencyCode(),$amount);

            $request['AMT'] = number_format((float)$amount, 2, '.', '');
            $request['ITEMAMT'] = number_format((float)$amount, 2, '.', '');
            $request['TAXAMT'] = 0;
            $request['SHIPPINGAMT'] = 0;
        }

        $response = $this->call('DoReferenceTransaction', $request);
        $this->_importFromResponse($this->_doReferenceTransactionResponse, $response);
    }
}