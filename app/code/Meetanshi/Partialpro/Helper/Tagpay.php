<?php

namespace Meetanshi\Partialpro\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\OrderFactory;

class Tagpay extends AbstractHelper
{
    const CONFIG_TAGPAY_ACTIVE = 'payment/tagpay/active';
    const CONFIG_TAGPAY_MODE = 'payment/tagpay/mode';
    const CONFIG_TAGPAY_INSTRUCTIONS = 'payment/tagpay/instructions';
    const CONFIG_TAGPAY_LIVE_MERCHANT_ID = 'payment/tagpay/live_merchant_id';
    const CONFIG_TAGPAY_SANDBOX_MERCHANT_ID = 'payment/tagpay/sandbox_merchant_id';
    const CONFIG_TAGPAY_LIVE_GATEWAY_URL = 'payment/tagpay/live_tagpay_url';
    const CONFIG_TAGPAY_SANDBOX_GATEWAY_URL = 'payment/tagpay/sandbox_tagpay_url';
    const CONFIG_TAGPAY_LOGO = 'payment/tagpay/show_logo';

    protected $directoryList;
    protected $storeManager;
    protected $request;
    protected $encryptor;
    private $repository;
    private $orderFactory;

    public function __construct(Context $context, EncryptorInterface $encryptor, DirectoryList $directoryList, StoreManagerInterface $storeManager, Http $request, Repository $repository, OrderFactory $orderFactory)
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
        $this->directoryList = $directoryList;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->repository = $repository;
        $this->orderFactory = $orderFactory;
    }

    public function isActive()
    {
        return $this->scopeConfig->getValue(self::CONFIG_TAGPAY_ACTIVE, ScopeInterface::SCOPE_STORE);
    }

    public function getPaymentInstructions()
    {
        return $this->scopeConfig->getValue(self::CONFIG_TAGPAY_INSTRUCTIONS, ScopeInterface::SCOPE_STORE);
    }

    public function getMerchantId()
    {
        if ($this->getMode()) {
            return $this->encryptor->decrypt($this->scopeConfig->getValue(self::CONFIG_TAGPAY_SANDBOX_MERCHANT_ID, ScopeInterface::SCOPE_STORE));
        } else {
            return $this->encryptor->decrypt($this->scopeConfig->getValue(self::CONFIG_TAGPAY_LIVE_MERCHANT_ID, ScopeInterface::SCOPE_STORE));
        }
    }

    public function getMode()
    {
        return $this->scopeConfig->getValue(self::CONFIG_TAGPAY_MODE, ScopeInterface::SCOPE_STORE);
    }

    public function getPaymentForm($order, $purchaseRef, $currency, $amount,$isInstallment)
    {
        $billingAddress = $order->getBillingAddress()->getData();
        $sessionId = $this->getSessionId();
        $currencyCode = $this->getCountryCode($currency);

        if($isInstallment){
            $acceptUrl = $this->getAcceptUrl();
            $cancelUrl = $this->getCancelUrl();
            $declineUrl = $this->getDeclineUrl();
        }else{
            $acceptUrl = $this->getTagpayAcceptUrl();
            $cancelUrl = $this->getTagpayCancelUrl();
            $declineUrl = $this->getTagpayDeclineUrl();
        }

        $html = "<form id='TagPayForm' name='tagpaysubmit' action='" . $this->getGatewayUrl() . "/online/online.php' method='POST'>";
        $html .= "<input type='hidden' name='sessionid' value='" . $sessionId['1'] . "' />";
        $html .= "<input type='hidden' name='merchantid' value='" . $this->getMerchantId() . "' />";
        $html .= "<input type='hidden' name='amount' value='" . $amount * 100 . "' />";
        $html .= "<input type='hidden' name='currency' value='" . $currencyCode . "' />";
        $html .= "<input type='hidden' name='purchaseref' value='" . $purchaseRef . "' />";
        $html .= "<input type='hidden' name='phonenumber' value='" . $billingAddress['telephone'] . "' />";
        $html .= "<input type='hidden' name='description' value='" . $this->getPaymentSubject() . "' />";
        $html .= "<input type='hidden' name='accepturl' value='" . $acceptUrl . "' />";
        $html .= "<input type='hidden' name='cancelurl' value='" . $cancelUrl . "' />";
        $html .= "<input type='hidden' name='declineurl' value='" . $declineUrl . "' />";
        $html .= "<input type='submit' name='ok' value='Payment' style='display:none' />";
        $html .= "</form>";

        return $html;
    }

    public function getSessionId()
    {
        $ch = curl_init();
        $url = $this->getGatewayUrl() . '/online/online.php?merchantid=' . $this->getMerchantId();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $jsonData = curl_exec($ch);
        $data = explode(':', $jsonData);

        //$data = ['OK', '321bcefb0eceecbeb2be302c89493b2a'];

        if ($data['0'] == 'NOK') {
            throw new LocalizedException(__('There is a processing your request : ' . $data[1]));
        }
        return $data;
    }

    public function getGatewayUrl()
    {
        if ($this->getMode()) {
            return $this->scopeConfig->getValue(self::CONFIG_TAGPAY_SANDBOX_GATEWAY_URL, ScopeInterface::SCOPE_STORE);
        } else {
            return $this->scopeConfig->getValue(self::CONFIG_TAGPAY_LIVE_GATEWAY_URL, ScopeInterface::SCOPE_STORE);
        }
    }

    public function getPaymentSubject()
    {
        $subject = trim($this->scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE));
        if (!$subject) {
            return "Magento 2 order";
        }

        return $subject;
    }

    public function getAcceptUrl()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        return $baseUrl . "partialpayment/tagpay/accept";
    }

    public function getCancelUrl()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        return $baseUrl . "partialpayment/tagpay/cancel";
    }

    public function getDeclineUrl()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        return $baseUrl . "partialpayment/tagpay/decline";
    }

    public function getTagpayAcceptUrl()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        return $baseUrl . "tagpay/payment/accept";
    }

    public function getTagpayCancelUrl()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        return $baseUrl . "tagpay/payment/cancel";
    }

    public function getTagpayDeclineUrl()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        return $baseUrl . "tagpay/payment/decline";
    }


    public function getCountryCode($code)
    {
        $data = [
            'AFA' => '971',
            'AWG' => '533',
            'AUD' => '036',
            'ARS' => '032',
            'AZN' => '944',
            'BSD' => '044',
            'BDT' => '050',
            'BBD' => '052',
            'BYR' => '974',
            'BOB' => '068',
            'BRL' => '986',
            'GBP' => '826',
            'BGN' => '975',
            'KHR' => '116',
            'CAD' => '124',
            'KYD' => '136',
            'CLP' => '152',
            'CNY' => '156',
            'COP' => '170',
            'CRC' => '188',
            'HRK' => '191',
            'CPY' => '196',
            'CZK' => '203',
            'DKK' => '208',
            'DOP' => '214',
            'XCD' => '951',
            'EGP' => '818',
            'ERN' => '232',
            'EEK' => '233',
            'EUR' => '978',
            'GEL' => '981',
            'GHC' => '288',
            'GIP' => '292',
            'GTQ' => '320',
            'HNL' => '340',
            'HKD' => '344',
            'HUF' => '348',
            'ISK' => '352',
            'INR' => '356',
            'IDR' => '360',
            'ILS' => '376',
            'JMD' => '388',
            'JPY' => '392',
            'KZT' => '368',
            'KES' => '404',
            'KWD' => '414',
            'LVL' => '428',
            'LBP' => '422',
            'LTL' => '440',
            'MOP' => '446',
            'MKD' => '807',
            'MGA' => '969',
            'MYR' => '458',
            'MTL' => '470',
            'BAM' => '977',
            'MUR' => '480',
            'MXN' => '484',
            'MZM' => '508',
            'NPR' => '524',
            'ANG' => '532',
            'TWD' => '901',
            'NZD' => '554',
            'NIO' => '558',
            'NGN' => '566',
            'KPW' => '408',
            'NOK' => '578',
            'OMR' => '512',
            'PKR' => '586',
            'PYG' => '600',
            'PEN' => '604',
            'PHP' => '608',
            'QAR' => '634',
            'RON' => '946',
            'RUB' => '643',
            'SAR' => '682',
            'CSD' => '891',
            'SCR' => '690',
            'SGD' => '702',
            'SKK' => '703',
            'SIT' => '705',
            'ZAR' => '710',
            'KRW' => '410',
            'LKR' => '144',
            'SRD' => '968',
            'SEK' => '752',
            'CHF' => '756',
            'TZS' => '834',
            'THB' => '764',
            'TTD' => '780',
            'TRY' => '949',
            'AED' => '784',
            'USD' => '840',
            'UGX' => '800',
            'UAH' => '980',
            'UYU' => '858',
            'UZS' => '860',
            'VEB' => '862',
            'VND' => '704',
            'AMK' => '894',
            'ZWD' => '716',
            'XOF' => '952'
        ];

        if (array_key_exists($code, $data)) {
            return $data[$code];
        }
    }
}
