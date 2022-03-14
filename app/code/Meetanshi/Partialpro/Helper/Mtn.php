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

class Mtn extends AbstractHelper
{
    const CONFIG_SANDBOX_MODE = 'payment/mtn/mode';
    const CONFIG_BILLMAP_CODE = 'payment/mtn/billmapcode';
    const CONFIG_BILLMAP_PASS = 'payment/mtn/password';
    const CONFIG_SANDBOX_URL = 'payment/mtn/sandbox_gateway';
    const CONFIG_LIVE_URL = 'payment/mtn/live_gateway';

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

    public function getBillMapCode()
    {
        return $this->scopeConfig->getValue(self::CONFIG_BILLMAP_CODE, ScopeInterface::SCOPE_STORE);
    }

    public function getBillMapPass()
    {
        return $this->encryptor->decrypt($this->scopeConfig->getValue(self::CONFIG_BILLMAP_PASS, ScopeInterface::SCOPE_STORE));
    }
}
