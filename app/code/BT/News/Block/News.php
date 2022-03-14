<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace BT\News\Block;

/**
 * Class News
 * @package Kitchen365\News\Block
 */
class News extends \Magento\Framework\View\Element\Template
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    protected $_storeManager;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_newsCollection;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * News constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \BT\News\Model\ResourceModel\News\CollectionFactory $newsCollection,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->_newsCollection = $newsCollection;
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getNewsCollection()
    {
        $collection = $this->_newsCollection->create()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('status', '1');
        return $collection;
    }
    // public function getNewsAllUrl()
    // {
    //     return $this->getUrl('news/index/announcement', ['_secure' => true]);
    // }
    // public function getDashbordNewsCollection()
    // {
    //     $collection = $this->_newsCollection->create()
    //                     ->addFieldToSelect('*')
    //                     ->addFieldToFilter('customergroup', ['finset' => $this->getGroupId()])
    //                     ->addFieldToFilter('status', '1')
    //                     ->setOrder('eventdate', 'ASC')
    //                     ->setPageSize(4);
    //     $dataCollection = [];
    //     $sequenc_data_image = [];
    //     $sequenc_data = [];
    //     foreach ($collection as $value) {
    //         if ($value->getImageupload()) {
    //             $sequenc_data_image[] = $value->getData();
    //         } else {
    //             $sequenc_data[] = $value->getData();
    //         }
    //     }

    //     if (count($sequenc_data_image) == 4) {
    //         array_pop($sequenc_data_image);
    //         array_pop($sequenc_data_image);
    //         $dataCollection = $sequenc_data_image;
    //     } else {
    //         if (count($sequenc_data_image) !== 1) {
    //             if (!empty($sequenc_data_image) && !empty($sequenc_data)) {
    //                 $dataCollection = array_merge($sequenc_data_image, $sequenc_data);
    //             } else {
    //                 if (!empty($sequenc_data)) {
    //                     $dataCollection = $sequenc_data;
    //                 }
    //             }
    //             if (count($sequenc_data_image) > 0) {
    //                 array_pop($dataCollection);
    //                 array_pop($dataCollection);
    //             } else {
    //                 $dataCollection = $sequenc_data;
    //             }
    //         } else {
    //             $dataCollection = array_merge($sequenc_data_image, $sequenc_data);
    //             array_pop($dataCollection);
    //         }
    //     }
    //     return $dataCollection;
    // }
    // public function getGroupId()
    // {
    //     return $this->_customerSession->getCustomer()->getGroupId();
    // }
    // public function getAjaxNewsCollection($pageNo)
    // {
    //     $collection = $this->_newsCollection->create()
    //                     ->addFieldToSelect('*')
    //                     ->addFieldToFilter(
    //                         'customergroup',
    //                         [['finset' => $this->getGroupId()],['eq' => '']]
    //                     )
    //                     ->setOrder('eventdate', 'ASC')
    //                     ->addFieldToFilter('status', '1')
    //                     ->setPageSize($this->getPageNo())
    //                     ->setCurPage($pageNo);
    //     return $collection;
    // }
    // public function getPageNo()
    // {
    //     return $this->scopeConfig->getValue('news/news/pageno', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    // }
    // public function getNoNewsImage()
    // {
    //     $imagePath =  $this->scopeConfig->getValue('news/news/no_news_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    //     return $this->getUrl('pub/media') . 'news/' . $imagePath;
    // }
    // public function getStoreId()
    // {
    //     return $this->_storeManager->getStore()->getId();
    // }
}
