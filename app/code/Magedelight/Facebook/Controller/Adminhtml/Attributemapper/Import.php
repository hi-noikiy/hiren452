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
namespace Magedelight\Facebook\Controller\Adminhtml\Attributemapper;

use Magento\Backend\App\Action\Context;
use Magedelight\Facebook\Model\AttributemapFactory;
use Magedelight\Facebook\Api\AttributemapRepositoryInterface;
use Magedelight\Facebook\Model\FbattributesFactory;
use Magento\Framework\File\Csv;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollFactory;

class Import extends \Magento\Backend\App\Action
{
    /**
     *
     * @var AttributemapFactory 
     */
    protected $attributeMapFactory;
    
    /**
     *
     * @var FbattributesFactory 
     */
    protected $fbattributesFactory;
    
    /**
    * CSV Processor
    *
    * @var \Magento\Framework\File\Csv
    */
    protected $csvProcessor;
    
    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        Context $context,
        AttributemapFactory $attributeMapFactory,
        AttributemapRepositoryInterface $attributemapRepository,
        FbattributesFactory $fbattributesFactory,
        Csv $csvProcessor,
        AttributeCollFactory $attrCollFactory    
       ) {
            $this->attributeMapFactory = $attributeMapFactory;
            $this->attributemapRepository = $attributemapRepository;
            $this->fbattributesFactory = $fbattributesFactory;
            $this->csvProcessor = $csvProcessor;
            $this->attrCollFactory = $attrCollFactory;
            parent::__construct($context);
    }
    
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $files = $this->getRequest()->getFiles();
        if(!isset($files['importfile'])){
            // error
        }
        $file = $files['importfile'];
        if(!isset($file['tmp_name'])){
           $this->messageManager->addError(__("Something went wrong"));
           return $resultRedirect->setPath('*/*/importrequest');
        }
        $mageattrData = $this->getAttributes();
        $importProductRawData = $this->csvProcessor->getData($file['tmp_name']);
        $csvdata = [];
        $removedata = [];
         /* code for get all editable mapped attributes */
        $editfbAttribute = $this->fbattributesFactory->create()->getCollection()
                                ->addFieldToFilter('editable',\Magedelight\Facebook\Model\Attributemap::IS_EDITABLE_YES)
                                ->addFieldToSelect('fb_attribute_code');
       
        $attributeDbMap = array_column($editfbAttribute->getData(), 'fb_attribute_code');
        /* end code for get all editable mapped attributes */
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
       
        
        $attributeMapArray = $csvdata;
        foreach ($attributeMapArray as $attributeMap) {
            if(!isset($attributeMap['fb_attribute']) || !isset($attributeMap['mage_attribute'])){
                $this->messageManager->addError(__("CSV is not valid."));
                return $resultRedirect->setPath('*/*/importrequest');
            }
            
            if(!in_array($attributeMap['fb_attribute'], $attributeDbMap) || !in_array($attributeMap['mage_attribute'], $mageattrData)){
                continue;
            }
            $attributeMapColl = $this->attributeMapFactory->create()->getCollection()
                                     ->addFieldToFilter('fb_attribute',$attributeMap['fb_attribute']);
            $attributeMapModel = $this->attributeMapFactory->create();
            if($attributeMapColl->getSize() > 0){
                $mappingId = $attributeMapColl->getFirstItem()->getId();
                $attributeMapModel = $this->attributemapRepository->getById($mappingId);
                $attributeMapModel->setFbAttribute($attributeMap['fb_attribute']);
                $attributeMapModel->setMageAttribute($attributeMap['mage_attribute']);
                $this->attributemapRepository->save($attributeMapModel);
            }
            else{
                $attributeMapModel->setFbAttribute($attributeMap['fb_attribute']);
                $attributeMapModel->setMageAttribute($attributeMap['mage_attribute']);
                $this->attributemapRepository->save($attributeMapModel);
            }
        }
        foreach ($removedata as $removeattr) {
            if(!isset($removeattr['fb_attribute']) || !isset($removeattr['mage_attribute'])){
                $this->messageManager->addError(__("CSV is not valid."));
                return $resultRedirect->setPath('*/*/importrequest');
            }
            if(!in_array($removeattr['fb_attribute'], $attributeDbMap) || !in_array($removeattr['mage_attribute'], $mageattrData) ){
                continue;
            }
            $attributeMapColl = $this->attributeMapFactory->create()->getCollection()
                                     ->addFieldToFilter('fb_attribute',$removeattr['fb_attribute']);
            if($attributeMapColl->getSize()){
                $mappingId = $attributeMapColl->getFirstItem()->getId();
                $this->attributemapRepository->deleteById($mappingId);
            }
        }
        $this->messageManager->addSuccess(__("Attribute Mapping saved successfully"));
        return $resultRedirect->setPath('*/*/importrequest');
       
    }
    
    /**
     * 
     * @return array
     */
    protected function getAttributes()
    {
        $collection = $this->attrCollFactory->create();
        $attr_groups = array();
        foreach ($collection as $items) {
            $attr_groups[] = $items->getData();
        }
        $attributeMageData = array_column($attr_groups, 'attribute_code');
        return $attributeMageData; 
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
