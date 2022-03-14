<?php

namespace Meetanshi\Partialpro\Model\Payment;

use Meetanshi\Partialpro\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\PaymentFactory;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Message\ManagerInterface;
use Magento\Braintree\Model\Adapter\BraintreeAdapterFactory;
use Magento\Framework\Json\Helper\Data as resultJson;

class Braintree
{
    const REQUEST_TYPE_AUTH_ONLY = 'AUTH_ONLY';
    const STATUS_APPROVED = 'APPROVED';

    protected $adapter;
    protected $paymentFactory;
    protected $messageManager;
    protected $request;
    protected $encryptor;
    protected $partialHelper;
    protected $scopeConfig;
    protected $adapterFactory;
    protected $resultJson;

    protected $paymentCardSaveToken;

    public function __construct(
        Data $partialHelper,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        PaymentFactory $paymentFactory,
        Encryptor $encryptor,
        ManagerInterface $messageManager,
        resultJson $resultJson,
        BraintreeAdapterFactory $adapterFactory
    )
    {
        $this->paymentFactory = $paymentFactory;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->encryptor = $encryptor;
        $this->partialHelper = $partialHelper;
        $this->scopeConfig = $scopeConfig;
        $this->resultJson = $resultJson;
        $this->adapterFactory = $adapterFactory;
    }

    public function payInstallment($order, $details)
    {
        $response = [];
        $this->adapter = $this->adapterFactory->create();
        $params = $this->request->getParams();
        $token = false;

        if (empty($params)) {
            $params['payment'] = $details;
        }

        $payment = $this->paymentFactory->create()->setMethod('braintree');
        $requestType = $this->scopeConfig->getValue('payment/' . $payment->getMethod() . '/payment_action');

        $amount = $details['amount'];

        if ($requestType == 'authorize') {
            $capture = false;
        } else {
            $capture = true;
        }

        $payment->setOrder($order);

        if (isset($params['payment']['cc_cid'])) {
            $cvvNumber = $params['payment']['cc_cid'];
        } else {
            $cvvNumber = '';
        }

        if (!isset($params['payment']['cc_token'])) {
            $payment->setCcNumber($params['payment']['cc_number'])
                ->setCcCid($cvvNumber)
                ->setCcExpMonth($params['payment']['cc_exp_month'])
                ->setCcExpYear($params['payment']['cc_exp_year']);
        }

        if ($amount <= 0) {
            $response['success'] = false;
            $response['errorMessage'] = "Invalid amount for authorization.";
            return $response;
        }

        if (isset($params['payment']['cc_token'])) {
            $token = $this->encryptor->decrypt($params['payment']['cc_token']);
            $token = $this->adapter->createNonce($token);
            $res = $this->captureSaveCard($order, $payment, $amount, $capture, $token->paymentMethodNonce->nonce);
        } else {
            $res = $this->captureNewCard($order, $payment, $amount, $capture, $token);
        }

        $response['success'] = $res;
        return $response;


    }

    protected function captureSaveCard($order, $payment, $amount, $capture, $token)
    {
        $res = true;
        $orderPaid = $order->getTotalPaid();
        $orderDue = $order->getTotalDue();

        $orderId = $order->getIncrementId();

        $billing = $order->getBillingAddress();
        $shipping = $order->getShippingAddress();

        $transactionParams = array(
            'orderId' => $orderId,
            'amount' => number_format($amount, 2),
            'customer' => array(
                'firstName' => $billing->getFirstname(),
                'lastName' => $billing->getLastname(),
                'company' => $billing->getCompany(),
                'phone' => $billing->getTelephone(),
                'fax' => $billing->getFax(),
                'email' => $order->getCustomerEmail(),
            ),
            'billing' => array(
                'firstName' => $billing->getFirstname(),
                'lastName' => $billing->getLastname(),
                'company' => $billing->getCompany(),
                'streetAddress' => $billing->getStreet()[0],
                'region' => $billing->getRegion(),
                'locality' => $billing->getCity(),
                'postalCode' => $billing->getPostCode(),
                'countryCodeAlpha2' => $billing->getCountryId(),
            ),
            'shipping' => array(
                'firstName' => $shipping->getFirstname(),
                'lastName' => $shipping->getLastname(),
                'company' => $shipping->getCompany(),
                'streetAddress' => $shipping->getStreet()[0],
                'region' => $shipping->getRegion(),
                'locality' => $shipping->getCity(),
                'postalCode' => $shipping->getPostCode(),
                'countryCodeAlpha2' => $shipping->getCountryId(),
            ),
        );
        if ($capture) {
            $transactionParams['options']['submitForSettlement'] = true;
        } else {
            $transactionParams['options']['submitForSettlement'] = false;
        }
        if ($token) {
            $transactionParams['paymentMethodNonce'] = $token;
        }
        try {
            $response = $this->adapter->sale($transactionParams);

            if (isset($response->success) && $response->success == 1) {
                $csToRequestMap = self::REQUEST_TYPE_AUTH_ONLY;
                $cardlastdigit = $response->transaction->creditCard['last4'];
                $payment->setAnetTransType($csToRequestMap);
                $payment->setAmount($amount);
                $payment->setCcLast4($cardlastdigit);
                $payment->setCcType($response->transaction->creditCard['cardType']);

                $payment->setLastTransId($response->transaction->id)
                    ->setCcTransId($response->transaction->id)
                    ->setTransactionId($response->transaction->id)
                    ->setIsTransactionClosed(0)
                    ->setStatus(self::STATUS_APPROVED)
                    ->setCcAvsStatus($response->transaction->avsPostalCodeResponseCode);

                if (!empty($response->transaction->cvvResponseCode) && isset($response->transaction->cvvResponseCode)) {
                    $payment->setCcCidStatus($response->transaction->cvvResponseCode);
                }

                $transId = $response->transaction->id;
                $formatAmount = $this->partialHelper->getFormattedPrice($order->getBaseCurrencyCode(), $amount);
                $message = "Captured amount of $formatAmount online. Transaction ID: $transId";

                if ($capture) {
                    $orderPaid += $amount;
                    $orderDue -= $amount;

                    $payment->setSkipTransactionCreation(true)
                        ->setPaidAmount($orderPaid)
                        ->setDueAmount($orderDue);

                    if (!empty($message) && $message != '-') {
                        $order->addStatusHistoryComment($message)->save();
                    }

                } else {
                    $payment->setSkipTransactionCreation(true);
                }

            } else {
                $exceptionMessage = $response->message;
                \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($exceptionMessage);
                $res = false;
            }
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
            $res = false;
        }
        return $res;
    }

    protected function captureNewCard($order, $payment, $amount, $capture, $token)
    {
        $res = true;
        $postCard = $this->request->getParam('payment');

        $orderId = $order->getIncrementId() . '-' . $payment['installments'] . "-" . date('Y-m-d');

        $orderPaid = $order->getTotalPaid();
        $orderDue = $order->getTotalDue();

        $billing = $order->getBillingAddress();
        $shipping = $order->getShippingAddress();

        if (isset($postCard['cc_cid'])) {
            $cvvNumber = $postCard['cc_cid'];
        } else {
            $cvvNumber = '';
        }

        $transactionParams = array(
            'orderId' => $orderId,
            'amount' => number_format($amount, 2),
            'customer' => array(
                'firstName' => $billing->getFirstname(),
                'lastName' => $billing->getLastname(),
                'company' => $billing->getCompany(),
                'phone' => $billing->getTelephone(),
                'fax' => $billing->getFax(),
                'email' => $order->getCustomerEmail(),
            ),
            'billing' => array(
                'firstName' => $billing->getFirstname(),
                'lastName' => $billing->getLastname(),
                'company' => $billing->getCompany(),
                'streetAddress' => $billing->getStreet()[0],
                'region' => $billing->getRegion(),
                'locality' => $billing->getCity(),
                'postalCode' => $billing->getPostCode(),
                'countryCodeAlpha2' => $billing->getCountryId(),
            ),
            'shipping' => array(
                'firstName' => $shipping->getFirstname(),
                'lastName' => $shipping->getLastname(),
                'company' => $shipping->getCompany(),
                'streetAddress' => $shipping->getStreet()[0],
                'region' => $shipping->getRegion(),
                'locality' => $shipping->getCity(),
                'postalCode' => $shipping->getPostCode(),
                'countryCodeAlpha2' => $shipping->getCountryId(),
            ),
        );

        $transactionParams['options']['storeInVaultOnSuccess'] = true;

        if ($capture) {
            $transactionParams['options']['submitForSettlement'] = true;
        } else {
            $transactionParams['options']['submitForSettlement'] = false;
        }
        if ($token) {
            $transactionParams['paymentMethodNonce'] = $token;
        } else {
            $transactionParams['creditCard'] = array(
                'cardholderName' => $billing->getFirstname() . ' ' . $billing->getLastname(),
                'number' => $postCard['cc_number'],
                'cvv' => $cvvNumber,
                'expirationDate' => $postCard['cc_exp_month'] . '/' . $postCard['cc_exp_year']
            );
        }

        $vaultCard = [];
        $detail_card = [];
        try {

            $response = $this->adapter->sale($transactionParams);

            if (isset($response->success) && $response->success == 1) {

                $csToRequestMap = self::REQUEST_TYPE_AUTH_ONLY;

                $cardlastdigit = $response->transaction->creditCard['last4'];
                $payment->setAnetTransType($csToRequestMap);
                $payment->setAmount($amount);
                $payment->setCcLast4($cardlastdigit);
                $payment->setCcType($response->transaction->creditCard['cardType']);

                $payment->setLastTransId($response->transaction->id)
                    ->setCcTransId($response->transaction->id)
                    ->setTransactionId($response->transaction->id)
                    ->setIsTransactionClosed(0)
                    ->setStatus(self::STATUS_APPROVED)
                    ->setCcAvsStatus($response->transaction->avsPostalCodeResponseCode);

                if (!empty($response->transaction->cvvResponseCode) && isset($response->transaction->cvvResponseCode)) {
                    $payment->setCcCidStatus($response->transaction->cvvResponseCode);
                }

                $transId = $response->transaction->id;
                $formatAmount = $this->partialHelper->getFormattedPrice($order->getBaseCurrencyCode(), $amount);
                $message = "Captured amount of $formatAmount online. Transaction ID: $transId";

                $vaultCard['gateway_token'] = $response->transaction->creditCard['token'];
                $vaultCard['customer_id'] = $order->getCustomerId();

                $vaultCard['is_active'] = false;
                $vaultCard['is_visible'] = false;

                $vaultCard['payment_method_code'] = $payment->getMethod();

                $vaultCard['type'] = '';
                $expires_at = date('Y-m-d', strtotime('+1 month', strtotime($response->transaction->creditCard['expirationYear'] . '-' . $response->transaction->creditCard['expirationMonth'] . '-01')));
                $vaultCard['expires_at'] = $expires_at;

                $detail_card['type'] = $this->getOptionText($response->transaction->creditCard['cardType'])->getText();
                $detail_card['maskedCC'] = $response->transaction->creditCard['last4'];
                $detail_card['expirationDate'] = $response->transaction->creditCard['expirationMonth'] . '/' . $response->transaction->creditCard['expirationYear'];

                $resultJsonFactory = $this->resultJson;
                $cartJsondetails = $resultJsonFactory->jsonEncode($detail_card);
                $vaultCard['details'] = $cartJsondetails;
                $vaultCard['public_hash'] = $this->genPublicHash($vaultCard);

                $paymentTokenModel = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Vault\Model\PaymentToken');
                $cardExits = $paymentTokenModel->getCollection()->addFieldToFilter('public_hash', array("eq" => $vaultCard['public_hash']));

                if (sizeof($cardExits->getData()) == 0) {
                    $paymentCardSaveToken = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Vault\Model\PaymentToken');
                    $paymentCardSaveToken->setData($vaultCard)->save();
                }

                if ($capture) {
                    $orderPaid += $amount;
                    $orderDue -= $amount;

                    $payment->setSkipTransactionCreation(true)
                        ->setPaidAmount($orderPaid)
                        ->setDueAmount($orderDue);


                    if (!empty($message) && $message != '-') {
                        $order->addStatusHistoryComment($message)->save();
                    }

                } else {
                    $payment->setSkipTransactionCreation(true);
                }

            } else {
                $exceptionMessage = $response->message;
                $this->messageManager->addErrorMessage(__($exceptionMessage));
                $res = false;
            }
        } catch (\Exception $e) {
            $res = false;
            $this->messageManager->addErrorMessage(__('Gateway request error: %s', $e->getMessage()));
        }

        return $res;
    }


    public static function getAllCardsCodes()
    {
        return ['Visa' => __('VI'), 'American Express' => __('AE'), 'MasterCard' => __('MC'), 'Discover' => __('DI'), 'JCB' => __('JCB'), 'Switch/Maestro' => __('SM'), 'Diners' => __('DN'), 'Solo' => __('SO'), 'Maestro International' => __('MI'), 'Maestro Domestic' => __('MD'), 'Other' => __('OT')];
    }

    public function getOptionText($optionId)
    {
        $options = self::getAllCardsCodes();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    protected function genPublicHash($vaultCard)
    {
        $hashKey = $vaultCard['gateway_token'];
        if ($vaultCard['customer_id']) {
            $hashKey = $vaultCard['customer_id'];
        }

        $hashKey .= $vaultCard['payment_method_code']
            . $vaultCard['type']
            . $vaultCard['details'];

        return $this->encryptor->getHash($hashKey);
    }
}