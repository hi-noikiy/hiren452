<?php

namespace Hiddentechies\Reviewspro\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_storeManager;
    protected $logoblock;
    protected $_orderCollection;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context, 
        \Magento\Theme\Block\Html\Header\Logo $logoblock,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection
    ) {
        $this->logoblock = $logoblock;
        $this->_storeManager = $storeManager;
        $this->_orderCollection = $orderCollection;
        parent::__construct($context);
    }

    public function getBaseUrl() {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getIsHome() {
        return $this->logoblock->isHomePage();
    }

    public function getMediaUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getConfigValue($value = '') {
        return $this->scopeConfig
                        ->getValue($value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function checkVerifiedReview($customerId, $productId) {
        $loopContinue = 0;
        if ($customerId != '' && $productId != '') {
            $orderProductArr = array();
            $orderCollection = $this->_orderCollection->create();
            $orderCollection->addAttributeToFilter('customer_id', $customerId)
                    ->addAttributeToFilter('status', 'complete')->load();
            $orderCount = $orderCollection->getSize();
            if ($orderCount > 0) {
                foreach ($orderCollection AS $order) {
                    foreach ($order->getAllItems() as $item) {
                        $proTempId = $item->getProductId();
                        if ($proTempId == $productId) {
                            $loopContinue = 1;
                            break;
                        }
                    }
                    if ($loopContinue == 1) {
                        break;
                    }
                }
            }
        }
        return $loopContinue;
    }
}
