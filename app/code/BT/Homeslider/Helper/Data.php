<?php

namespace BT\Homeslider\Helper;


class Data extends \Magento\Framework\Url\Helper\Data
{


    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function getCategoryCollection()
    {
        $categoryCollection = $this->_collectionFactory->create()
                                ->addAttributeToSelect('*')
                                ->setStore($this->_storeManager->getStore());
        // $blankArray = [];
        // foreach ($categoryCollection as $key => $value) {
        //     if($value->getHomeCategoryImage())
        //     {
        //         $blankArray[] = [
        //                 'category_url' => $value->getUrl(),
        //                 'slider_image' => 'http://127.0.0.1/aumika'.$value->getHomeCategoryImage()
        //             ];
        //     }
                                         
        // }
        return $categoryCollection;
    }
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}