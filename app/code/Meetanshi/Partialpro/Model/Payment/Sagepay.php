<?php

namespace Meetanshi\Partialpro\Model\Payment;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Meetanshi\Partialpro\Helper\Sagepay as SagepayHelper;
use Zend_Http_Response;

class Sagepay
{
    const TRANSECTION_TYPE = 'transactionType';
    const MERCHANT_SESSION_KEY = 'merchantSessionKey';
    const PAYMENT_METHOD = 'paymentMethod';
    const CARD = 'card';
    const CARD_IDENTIFIER = 'cardIdentifier';
    const CARD_SAVE = 'save';
    const HTTP_1 = '1.1';


    private $curl;
    private $sagepayHelper;
    private $encryptor;

    public function __construct(Curl $curl,
                                SagepayHelper $sagepayHelper,
                                EncryptorInterface $encryptor)
    {
        $this->curl = $curl;
        $this->sagepayHelper = $sagepayHelper;
        $this->encryptor = $encryptor;
    }

    public function payInstallment($order, $details)
    {
        $response = [];

        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        $multiply = 100;

        $amount = $details['amount'];

        if ($this->sagepayHelper->isDecimal($order->getOrderCurrencyCode())) {
            $multiply = 1;
        }
        $amount = round($amount * $multiply);

        $url = $this->sagepayHelper->getEndpointUrl('merchant-session-keys');
        $vendorName = trim($this->sagepayHelper->getVendorName());
        $encoded_credential = base64_encode(trim($this->sagepayHelper->getIntegrationKey())
            . ':' . trim($this->sagepayHelper->getIntegrationPass()));

        $params = '{ "vendorName": "' . $vendorName . '" }';
        $authorization = "Authorization: Basic " . $encoded_credential;
        $merchant = $this->sagepayHelper->generateCurlRequest($url, $params, $authorization);

        if ($merchant['status'] != 201 && $authorization != 200) {
            $response['success'] = false;
            return $response;
        }

        $cardurl = $this->sagepayHelper->getEndpointUrl('card-identifiers');

        $month = $this->formatMonth($details['payment']['cc_exp_month']);
        $year = substr($details['payment']['cc_exp_year'], 2, 3);
        $cardNumber = trim($details['payment']['cc_number']);
        $cardHolderName = $this->getName($billingAddress);

        $expiry = trim($month . $year);
        $cvn = trim($details['payment']['cc_cid']);

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
            $response['success'] = false;
            return $response;
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
            '"currency": "' . strtoupper($order->getOrderCurrencyCode()) . '",' .
            '"description": "Demo Transaction",' .
            '"apply3DSecure": "' . $this->sagepayHelper->getApply3DSecure() . '",' .
            '"applyAvsCvcCheck": "' . $this->sagepayHelper->getAVSCheck() . '",' .
            '"customerFirstName": "' . $billingAddress->getFirstname() . '",' .
            '"customerLastName": "' . $billingAddress->getLastname() . '",' .
            '"customerEmail": "' . $billingAddress->getEmail() . '",';
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

        $shippingStreet = $shippingAddress->getStreet();
        $shippingStreetOne = $shippingStreetTwo = 'a';
        if (sizeof($shippingStreet) >= 2) {
            $shippingStreetOne = $shippingStreet[0];
            $shippingStreetTwo = $shippingStreet[1];
        } else if (sizeof($shippingStreet) == 1) {
            $shippingStreetOne = $shippingStreetTwo = $shippingStreet[0];
        }


        if (!!$shippingAddress) {
            $tranParams .=
                '"shippingDetails": {' .
                '    "recipientFirstName": "' . $shippingAddress->getFirstname() . '",' .
                '    "recipientLastName": "' . $shippingAddress->getLastname() . '",' .
                '    "shippingAddress1": "' . $shippingStreetOne . '",' .
                '    "shippingAddress2": "' . $shippingStreetTwo . '",';
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

        $encoded_credential = base64_encode(trim($this->sagepayHelper->getIntegrationKey())
            . ':' . trim($this->sagepayHelper->getIntegrationPass()));

        $authorization = "Authorization: Basic " . $encoded_credential;


        $headers = [
            $authorization,
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ];
        $this->curl->write(
            'POST',
            $this->sagepayHelper->getEndpointUrl('transactions'),
            self::HTTP_1,
            $headers,
            $tranParams
        );

        $res = $this->read();
        $data = json_decode(trim($res));

        if ($data->statusCode != '0000') {
            $response['success'] = false;
            return $response;
        }

        $response['success'] = true;
        return $response;
    }

    private function formatMonth($month)
    {
        return !empty($month) ? sprintf('%02d', $month) : null;
    }

    private function getName($billingAddress)
    {
        return $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname();
    }

    public function read()
    {
        return Zend_Http_Response::fromString($this->curl->read())->getBody();
    }
}