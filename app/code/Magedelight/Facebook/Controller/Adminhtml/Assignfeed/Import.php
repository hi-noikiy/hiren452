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

use Magento\Backend\App\Action\Context;
use Magento\Framework\File\Csv;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class Import extends \Magento\Backend\App\Action
{
    /**
    * CSV Processor
    *
    * @var Csv
    */
    private $csvProcessor;
    
    /**
     *
     * @var type 
     */
    private $productRepository; 
    
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;
    
    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        Context $context,
        Csv $csvProcessor,
        ProductRepositoryInterface $productRepository,
        StoreRepositoryInterface $storeRepository    
       ) {
            $this->csvProcessor = $csvProcessor;
            $this->productRepository = $productRepository;
            $this->storeRepository = $storeRepository;
            parent::__construct($context);
    }
    
    public function execute()
    {
        $files = $this->getRequest()->getFiles();
        if(!isset($files['importfile'])){
            // error
        }
        $file = $files['importfile'];
        if(!isset($file['tmp_name'])){
           echo __('Invalid file upload attempt.');
           die();
        }
        $importProductRawData = $this->csvProcessor->getData($file['tmp_name']);
        $csvdata = [];
        $removedata = [];
        foreach ($importProductRawData as $rowIndex => $dataRow){
            if($rowIndex!=0){
                foreach ($dataRow as $colnum => $datacolumn) {
                    if($dataRow[2]=='add'){
                        $csvdata[$rowIndex][$importProductRawData[0][$colnum]] = $importProductRawData[$rowIndex][$colnum];
                    }
                    else{
                        $removedata[$rowIndex][$importProductRawData[0][$colnum]] = $importProductRawData[$rowIndex][$colnum];
                    }
                }
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $product = null;
        foreach ($csvdata as $addskudata) {
            try{
                if($addskudata['store_view_code']!=''){
                    $scopeCode = $addskudata['store_view_code'];
                    $storeId = $this->storeRepository->get($scopeCode)->getId();
                    $product = $this->productRepository->get($addskudata['sku'],true,$storeId);
                }
                else{
                    $product = $this->productRepository->get($addskudata['sku']);
                }
            } catch (\Exception $ex) {
                // log error
            }
            if($product!=null){
                $product->setIsAllowFacebookFeed(true);
                $product->save();
            }
        }
        foreach ($removedata as $removeskudata) {
            try{
                if($removeskudata['store_view_code']!=''){
                    $scopeCode = $removeskudata['store_view_code'];
                    $storeId = $this->storeRepository->get($scopeCode)->getId();
                    $product = $this->productRepository->get($removeskudata['sku'],true,$storeId);
                }
                else{
                    $product = $this->productRepository->get($removeskudata['sku']);
                }
            } catch (\Exception $ex) {
                // log error
            }
            if($product!=null){
                $product->setIsAllowFacebookFeed(false);
                $product->save();
            }
        }
        $this->messageManager->addSuccess(__("Product Feed has been updated successfully"));
        return $resultRedirect->setPath('*/*/prodindex');
    }
    
    /**
     * 
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }
}
