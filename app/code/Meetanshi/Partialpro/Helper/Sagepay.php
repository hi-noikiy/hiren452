<?php

namespace Meetanshi\Partialpro\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreResolver;

class Sagepay extends AbstractHelper
{
    const CONFIG_VENDOR_NAME = 'payment/sagepay/vendor_name';
    const CONFIG_INTEGRATION_KEY = 'payment/sagepay/integration_key';
    const CONFIG_INTEGRATION_PASS = 'payment/sagepay/integration_password';
    const CONFIG_SANDBOX_MODE = 'payment/sagepay/mode';
    const CONFIG_SANDBOX_URL = 'payment/sagepay/sandbox_gateway';
    const CONFIG_LIVE_URL = 'payment/sagepay/live_gateway';
    const CONFIG_SAVE_CARD = 'payment/sagepay/save_card';
    const CONFIG_PAYMENT_ACTION = 'payment/sagepay/payment_action';
    const CONFIG_GIFT_AID = 'payment/sagepay/gift_aid';
    const CONFIG_SAGEPAY_LOGO = 'payment/sagepay/show_logo';
    const CONFIG_SAGEPAY_INSTRUCTION = 'payment/sagepay/instructions';
    const CONFIG_SAGEPAY_DEBUG = 'payment/sagepay/debug';
    const CONFIG_3D_SECURE = 'payment/sagepay/allow_3d_secure';
    const CONFIG_AVS_CHECK = 'payment/sagepay/cvc_check';

    private $encryptor;
    private $curlFactory;
    private $storeResolver;
    private $storeManager;
    private $repository;
    private $request;

    public function __construct(Context $context, EncryptorInterface $encryptor, CurlFactory $curlFactory, StoreResolver $storeResolver, StoreManagerInterface $storeManager, Repository $repository, RequestInterface $request)
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
        $this->curlFactory = $curlFactory;
        $this->storeResolver = $storeResolver;
        $this->storeManager = $storeManager;
        $this->repository = $repository;
        $this->request = $request;
    }

    public function showLogo()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SAGEPAY_LOGO, ScopeInterface::SCOPE_STORE);
    }

    public function getInstructions()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SAGEPAY_INSTRUCTION, ScopeInterface::SCOPE_STORE);
    }

    public function getEndpointUrl($additionalPath)
    {
        return trim($this->getGatewayUrl()) . sprintf('/%s', $additionalPath);
    }

    public function getGatewayUrl()
    {
        if ($this->getMode()) {
            return $this->scopeConfig->getValue(self::CONFIG_SANDBOX_URL, ScopeInterface::SCOPE_STORE);
        } else {
            return $this->scopeConfig->getValue(self::CONFIG_LIVE_URL, ScopeInterface::SCOPE_STORE);
        }
    }

    public function getMode()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SANDBOX_MODE, ScopeInterface::SCOPE_STORE);
    }

    public function isLoggerEnabled()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SAGEPAY_DEBUG, ScopeInterface::SCOPE_STORE);
    }

    public function isSaveCard()
    {
        if ($this->scopeConfig->getValue(self::CONFIG_SAVE_CARD, ScopeInterface::SCOPE_STORE)) {
            return 'true';
        }
        return 'false';
    }

    public function getPaymentType()
    {
        $action = $this->scopeConfig->getValue(self::CONFIG_PAYMENT_ACTION, ScopeInterface::SCOPE_STORE);
        if ($action == 'authorize_capture') {
            return 'Payment';
        } else {
            return 'Deferred';
        }
    }

    public function getIntegrationKey()
    {
        return $this->encryptor->decrypt($this->scopeConfig->getValue(self::CONFIG_INTEGRATION_KEY, ScopeInterface::SCOPE_STORE));
    }

    public function getIntegrationPass()
    {
        return $this->encryptor->decrypt($this->scopeConfig->getValue(self::CONFIG_INTEGRATION_PASS, ScopeInterface::SCOPE_STORE));
    }

    public function generateCurlRequest($url, $params, $authorization)
    {
        $curl = $this->curlFactory->create();

        $headers = [
            $authorization,
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ];

        $method = \Zend_Http_Client::POST;

        if (!$params) {
            $method = \Zend_Http_Client::GET;
        }
        $curl->write(
            $method,
            $url,
            '1.1',
            $headers,
            $params
        );

        $rawResponse = $curl->read();
        $status = $curl->getInfo(CURLINFO_HTTP_CODE);
        $curl->close();

        $data = preg_split('/^\r?$/m', $rawResponse, 2);

        $data = json_decode(trim($data[1]));

        $response = [
            "status" => $status,
            "data" => $data
        ];

        return $response;
    }

    public function getStoreName()
    {
        return $this->storeManager->getStore()->getName();
    }

    public function getVendorTransactionCode($order)
    {
        return substr($order . "-" . date("Ymd") . "-" . time() . "-" . $this->getVendorName(), 0, 40);
    }

    public function getVendorName()
    {
        return $this->scopeConfig->getValue(self::CONFIG_VENDOR_NAME, ScopeInterface::SCOPE_STORE);
    }

    public function getApply3DSecure()
    {
        return $this->scopeConfig->getValue(self::CONFIG_3D_SECURE, ScopeInterface::SCOPE_STORE);
    }

    public function getAVSCheck()
    {
        return $this->scopeConfig->getValue(self::CONFIG_AVS_CHECK, ScopeInterface::SCOPE_STORE);
    }

    public function getAllowGiftAid()
    {
        if ($this->scopeConfig->getValue(self::CONFIG_GIFT_AID, ScopeInterface::SCOPE_STORE)) {
            return 'true';
        }
        return 'false';
    }

    public function isDecimal($currency)
    {
        return in_array(strtolower($currency), [
            'bif',
            'djf',
            'jpy',
            'krw',
            'pyg',
            'vnd',
            'xaf',
            'xpf',
            'clp',
            'gnf',
            'kmf',
            'mga',
            'rwf',
            'vuv',
            'xof'
        ]);
    }
}
