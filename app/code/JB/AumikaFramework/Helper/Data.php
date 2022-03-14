<?php

namespace JB\AumikaFramework\Helper;

use Magento\Store\Model\Store;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ObjectManager;

class Data extends \Magento\Framework\Url\Helper\Data
{


    protected $config = array();

     public function __construct(
        StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_storeManager = $storeManagerInterface;
        parent::__construct($context);
    }

    public function getScopeConfig($conf = NULL)
    {
        if($conf) 
         return $this->scopeConfig->getValue($conf, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $this->scopeConfig;
    }

    public function getServices($name)
    {
        return $this->scopeConfig->getValue(
            'jb_settings/general/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );      
    }


    public function getSupport($name)
    {
        return $this->scopeConfig->getValue(
            'jb_settings/custsupport/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );      
    }

    public function getItemCollection($name)
    {
        return $this->scopeConfig->getValue(
            'jb_settings/popularcollection/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );      
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

}