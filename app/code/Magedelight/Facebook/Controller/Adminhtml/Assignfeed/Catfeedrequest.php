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
use Magento\Store\Model\StoreRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\Product\ActionFactory;
use Magento\Catalog\Model\Indexer\Product\Price\Processor;

class Catfeedrequest extends \Magento\Backend\App\Action
{
    /**
     *
     * @var Magento\Catalog\Model\CategoryFactory 
     */
    protected $categoryFactory;
    
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
     * 
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        StoreManagerInterface $storeManager,
        StoreRepository $storeRepository,
        ActionFactory $actionFactory,
        Processor $productPriceIndexerProcessor    
       ) {
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
        $this->storeRepository = $storeRepository;
        $this->actionFactory = $actionFactory;
        $this->productPriceIndexerProcessor = $productPriceIndexerProcessor;
        parent::__construct($context);
    }
    public function _initAction()
    {
       $this->_view->loadLayout();
       return $this;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $storeId = $this->getRequest()->getParam('store', 0);
        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());
        $assign = $params['assign']; 
        $category = trim($params['category'],",");
        $message = '';
        if($assign == 'true'){
            $assign = true;
            $message = "assigned";
        }
        else{
            $assign = false;
            $message = "unassigned";
        }
        $categories = array_unique(explode(',', $category));
        try{
            foreach ($categories as $catId) {
                $category = $this->categoryFactory->create()
                            ->setStoreId($store)->load($catId);
                $categoryProducts = $category->getProductCollection()
                                             ->addAttributeToSelect('*');
                $productIds = [];
                foreach ($categoryProducts as $product) 
                {
                    $productIds[] = $product->getId();
                }
                $actionModel = $this->actionFactory->create(); 
                 if($storeId==0){
                    $storelist = $this->getAllStores();
                    foreach ($storelist as $store_id) {
                        $actionModel->updateAttributes($productIds, ['is_allow_facebook_feed' => $assign], (int) $store_id);
                    }
                }
                else{
                    $actionModel->updateAttributes($productIds, ['is_allow_facebook_feed' => $assign], (int) $storeId);
                }
                $this->productPriceIndexerProcessor->reindexList($productIds);
            }
            $this->messageManager->addSuccess(__('Categroy products for feed has been %1 successfully.',$message));
        } catch (Exception $ex) {
            $this->messageManager->addError(__('Something went wrong.'));
        }
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('md_facebook/assignfeed/catindex');
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
