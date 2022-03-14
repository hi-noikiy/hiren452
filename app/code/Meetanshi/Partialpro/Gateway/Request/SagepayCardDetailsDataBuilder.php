<?php

namespace Meetanshi\Partialpro\Gateway\Request;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Meetanshi\Partialpro\Helper\Sagepay as SagepayHelper;
use Magento\Checkout\Model\Session as CheckoutSession;

class SagepayCardDetailsDataBuilder implements BuilderInterface
{
    const TRANSECTION_TYPE = 'transactionType';
    const MERCHANT_SESSION_KEY = 'merchantSessionKey';
    const PAYMENT_METHOD = 'paymentMethod';
    const CARD = 'card';
    const CARD_IDENTIFIER = 'cardIdentifier';
    const CARD_SAVE = 'save';


    private $curl;
    private $sagepayHelper;
    private $encryptor;
    private $checkoutSession;

    public function __construct(CurlFactory $curl, SagepayHelper $sagepayHelper,CheckoutSession $checkoutSession, EncryptorInterface $encryptor)
    {
        $this->curl = $curl;
        $this->sagepayHelper = $sagepayHelper;
        $this->encryptor = $encryptor;
        $this->checkoutSession = $checkoutSession;
    }

    public function build(array $buildSubject)
    {

        $paymentDO = SubjectReader::readPayment($buildSubject);

        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        $multiply = 100;

        $quote = $this->checkoutSession->getQuote();
        $amount = $quote->getPartialPayNow();
        if ($amount <= 0) {
            $amount = SubjectReader::readAmount($buildSubject);
        }

        if ($this->sagepayHelper->isDecimal($order->getCurrencyCode())) {
            $multiply = 1;
        }
        $amount = round($amount * $multiply);
        ContextHelper::assertOrderPayment($payment);

        $url = $this->sagepayHelper->getEndpointUrl('merchant-session-keys');
        $vendorName = trim($this->sagepayHelper->getVendorName());
        $encoded_credential = base64_encode(trim($this->sagepayHelper->getIntegrationKey())
            . ':' . trim($this->sagepayHelper->getIntegrationPass()));

        $params = '{ "vendorName": "' . $vendorName . '" }';
        $authorization = "Authorization: Basic " . $encoded_credential;
        $merchant = $this->sagepayHelper->generateCurlRequest($url, $params, $authorization);


        if ($merchant['status'] != 201 && $authorization != 200) {
            $error = $merchant['data']->description;
            throw new LocalizedException(__($error));
        }

        $cardurl = $this->sagepayHelper->getEndpointUrl('card-identifiers');
        $data = $payment->getAdditionalInformation();
        $month = $this->formatMonth($data[OrderPaymentInterface::CC_EXP_MONTH]);
        $year = substr($data[OrderPaymentInterface::CC_EXP_YEAR], 2, 3);
        $cardNumber = $this->encryptor->decrypt($data[OrderPaymentInterface::CC_NUMBER_ENC]);
        $cardHolderName = $this->getName($billingAddress);
        $expiry = $month . $year;
        $cvn = $this->encryptor->decrypt($data["cc_cid_enc"]);

        $cardparams = '{' .
            '"cardDetails": {' .
            '    "cardholderName": "' . $cardHolderName . '",' .
            '    "cardNumber": "' . $cardNumber . '",' .
            '    "expiryDate": "' . $expiry . '",' .
            '    "securityCode": "' . $cvn . '"' .
            '}' .
            '}';

        $cardheader = "Authorization: Bearer " . $merchant['data']->merchantSessionKey;

        $cardResponse = $this->sagepayHelper->generateCurlRequest($cardurl, $cardparams, $cardheader);


        if ($cardResponse['status'] != 201 && $cardResponse['status'] != 200) {
            $carderror = $cardResponse['data']->description;
            throw new LocalizedException(__($carderror));
        }

        /** Transaction Values */
        $isSave = $this->sagepayHelper->isSaveCard();
        $giftAid = $this->sagepayHelper->getAllowGiftAid();

        $streetAddress1 = $billingAddress->getStreetLine1();
        $streetAddress2 = $billingAddress->getStreetLine2();
        $postCode = $billingAddress->getPostcode();

        if ($this->sagepayHelper->getMode()) {
            $streetAddress1 = "88";
            $streetAddress2 = "88";
            $postCode = "412";
        }

        $tranParams = '{' .
            '"transactionType": "' . $this->sagepayHelper->getPaymentType() . '",' .
            '"paymentMethod": {' .
            '    "card": {';

        $tranParams .=
            '"merchantSessionKey": "' . $merchant['data']->merchantSessionKey . '",' .
            '"cardIdentifier": "' . $cardResponse['data']->cardIdentifier . '",' .
            '"save": "' . $isSave . '",' .
            '"reusable": "false"' .
            '}' .
            '},' .
            '"vendorTxCode": "demotransaction' . time() . '",' .
            '"amount": ' . $amount . ',' .
            '"currency": "' . strtoupper($order->getCurrencyCode()) . '",' .
            '"description": "Demo Transaction",' .
            '"apply3DSecure": "' . $this->sagepayHelper->getApply3DSecure() . '",' .
            '"applyAvsCvcCheck": "' . $this->sagepayHelper->getAVSCheck() . '",' .
            '"customerFirstName": "' . $billingAddress->getFirstname() . '",' .
            '"customerLastName": "' . $billingAddress->getLastname() . '",' .
            '"customerEmail": "' . $billingAddress->getEmail() . '",' ;
            //'"customerPhone": "' . $billingAddress->getTelephone() . '",';
        $tranParams .=
            '"billingAddress": {' .
            '    "address1": "' . $streetAddress1 . '",' .
            '    "address2": "' . $streetAddress2 . '",';
        $tranParams .= '    "postalCode": "' . $postCode . '",';
        if ($billingAddress->getCountryId() == 'US') {
            $tranParams .= '    "state": "' . $billingAddress->getRegionCode() . '",';
        }

        $tranParams .= '    "city": "' . $billingAddress->getCity() . '",' .
            '    "country": "' . $billingAddress->getCountryId() . '"';
        $tranParams .= '},';

        if (!!$shippingAddress) {
            $tranParams .=
                '"shippingDetails": {' .
                '    "recipientFirstName": "' . $shippingAddress->getFirstname() . '",' .
                '    "recipientLastName": "' . $shippingAddress->getLastname() . '",' .
                '    "shippingAddress1": "' . $shippingAddress->getStreetLine1() . '",' .
                '    "shippingAddress2": "' . $shippingAddress->getStreetLine2() . '",';
            $tranParams .= '    "shippingPostalCode": "' . $shippingAddress->getPostcode() . '",';
            if ($shippingAddress->getCountryId() == 'US') {
                $tranParams .= '    "shippingState": "' . $shippingAddress->getRegionCode() . '",';
            }
            $tranParams .= '    "shippingCity": "' . $shippingAddress->getCity() . '",' .
                '    "shippingCountry": "' . $shippingAddress->getCountryId() . '"';
            $tranParams .= '},';
        }
        $tranParams .= '"giftAid": "' . $giftAid . '",';
        $tranParams .= '"entryMethod": "Ecommerce"';
        $tranParams .= '}';

        return [
            self::PAYMENT_METHOD => $tranParams
        ];
    }

    private function formatMonth($month)
    {
        return !empty($month) ? sprintf('%02d', $month) : null;
    }

    private function getName(AddressAdapterInterface $billingAddress)
    {
        return $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname();
    }
}
