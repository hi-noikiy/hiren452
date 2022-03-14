<?php

namespace BT\Jewellery\Helper;


class Data extends \Magento\Framework\Url\Helper\Data
{


    public function __construct(
        \BT\Jewellery\Model\ResourceModel\Jewellery\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function getDiamondJewelleryCollection()
    {
        $collection = $this->_collectionFactory->create()
                                ->addFieldToFilter('type','diamond');
        return $collection;
    }
    public function getGemestoneJewelleryCollection()
    {
        $collection = $this->_collectionFactory->create()
                                ->addFieldToFilter('type','gemstone');
        return $collection;
    }
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}