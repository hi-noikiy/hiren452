<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Facebook
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
namespace Magedelight\Facebook\Controller\Adminhtml\Assignfeed;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreRepository;
use Magento\Catalog\Model\Product\ActionFactory;
use Magento\Catalog\Model\Indexer\Product\Price\Processor;

class MassOptions extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     *
     * @var Magento\Store\Model\StoreManagerInterface 
     */
    protected $storeManager;

    /**
     * @var StoreRepository
     */
    protected $storeRepository;
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, 
                                Filter $filter, 
                                CollectionFactory $collectionFactory,
                                StoreManagerInterface $storeManager,
                                StoreRepository $storeRepository,
                                ActionFactory $actionFactory,
                                Processor $productPriceIndexerProcessor
                                )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->storeRepository = $storeRepository;
        $this->actionFactory = $actionFactory;
        $this->productPriceIndexerProcessor = $productPriceIndexerProcessor;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());
        $feedValue = $this->getRequest()->getParam('feed');
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $productIds = $collection->getAllIds();
        $actionModel = $this->actionFactory->create(); 
        if($storeId==0){
            $storelist = $this->getAllStores();
            foreach ($storelist as $store_id) {
                $actionModel->updateAttributes($productIds, ['is_allow_facebook_feed' => $feedValue], (int) $store_id);
            }
        }
        else{
            $actionModel->updateAttributes($productIds, ['is_allow_facebook_feed' => $feedValue], (int) $storeId);
        }
        $collectionSize = $collection->getSize();
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated', $collectionSize));
        $this->productPriceIndexerProcessor->reindexList($productIds);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('catalog/product/');
    }
    
    public function getAllStores()
    {
        $stores = $this->storeRepository->getList();
        $storeList = [];
        foreach ($stores as $store) {
            $storeId = $store["store_id"];
            $storeList[] = $storeId;
        }
        return $storeList;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
