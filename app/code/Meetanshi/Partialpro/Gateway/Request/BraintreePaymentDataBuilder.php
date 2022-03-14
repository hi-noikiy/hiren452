<?php

namespace Meetanshi\Partialpro\Gateway\Request;

use Magento\Braintree\Gateway\Config\Config;
use Magento\Braintree\Gateway\SubjectReader;
use Magento\Braintree\Observer\DataAssignObserver;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Magento\Checkout\Model\Session as CheckoutSession;
use Meetanshi\Partialpro\Helper\Data as partialHelper;
use Magento\Framework\App\State;
use Magento\Backend\Model\Session\Quote as BackendSession;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Framework\Serialize\Serializer\Json as jsonHelper;

class BraintreePaymentDataBuilder implements BuilderInterface
{
    use Formatter;

    const AMOUNT = 'amount';
    const PAYMENT_METHOD_NONCE = 'paymentMethodNonce';
    const MERCHANT_ACCOUNT_ID = 'merchantAccountId';
    const ORDER_ID = 'orderId';

    protected $subjectReader;
    protected $checkoutSession;
    protected $partialHelper;

    protected $state;
    protected $customerBackendSession;
    protected $total;
    protected $jsonHelper;

    public function __construct(Config $config,
                                SubjectReader $subjectReader,
                                CheckoutSession $checkoutSession,
                                partialHelper $partialHelper,
                                State $state,
                                Total $total,
                                BackendSession $customerBackendSession,
                                jsonHelper $jsonHelper)
    {
        $this->subjectReader = $subjectReader;
        $this->checkoutSession = $checkoutSession;
        $this->partialHelper = $partialHelper;
        $this->total = $total;
        $this->state = $state;
        $this->customerBackendSession = $customerBackendSession;
        $this->jsonHelper = $jsonHelper;
    }

    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();

        $quote = $this->getQuote();
        $amount = $quote->getPartialPayNow();

        if ($amount <= 0) {
            $amount = $this->subjectReader->readAmount($buildSubject);
        } else {
            $amount = $this->partialHelper->convertCurrency($quote->getBaseCurrencyCode(), $quote->getQuoteCurrencyCode(), $amount);
            if ($quote->getIsMultiShipping()) {

                $shippingAddresses = $quote->getAllShippingAddresses();

                $partialOrderInfo = $quote->getPartialOrder();
                $allOrderData = $this->jsonHelper->unserialize($partialOrderInfo);

                foreach ($allOrderData as $mainOrderId => $oriAddressId) {
                    if ($mainOrderId == $order->getOrderIncrementId()) {
                        $mainAddressId = $oriAddressId;
                    }
                }

                foreach ($shippingAddresses as $address) {
                    if ($mainAddressId == $address->getId()) {
                        $amount = $this->partialHelper->convertCurrency($quote->getBaseCurrencyCode(), $quote->getQuoteCurrencyCode(), $address->getPartialPayNow());
                    }
                }
            }

        }

        $result = [
            self::AMOUNT => $this->formatPrice($amount),
            self::PAYMENT_METHOD_NONCE => $payment->getAdditionalInformation(
                DataAssignObserver::PAYMENT_METHOD_NONCE
            ),
            self::ORDER_ID => $order->getOrderIncrementId()
        ];

        return $result;
    }

    private function getArea()
    {
        try {
            return $this->state->getAreaCode();
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
            return 'frontend';
        }
    }

    private function getQuote()
    {
        if ($this->getArea() == 'adminhtml') {
            return $this->customerBackendSession->getQuote();
        }
        return $this->checkoutSession->getQuote();
    }
}