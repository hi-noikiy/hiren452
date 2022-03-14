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

class Save extends \Magento\Backend\App\Action
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
     * 
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        Context $context,
        AttributemapFactory $attributeMapFactory,
        AttributemapRepositoryInterface $attributemapRepository,
        FbattributesFactory $fbattributesFactory    
       ) {
            $this->attributeMapFactory = $attributeMapFactory;
            $this->attributemapRepository = $attributemapRepository;
            $this->fbattributesFactory = $fbattributesFactory;
            parent::__construct($context);
    }
    
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        /* code for get all editable mapped attributes */
        $editfbAttribute = $this->fbattributesFactory->create()->getCollection()
                                ->addFieldToFilter('editable',\Magedelight\Facebook\Model\Attributemap::IS_EDITABLE_YES)
                                ->addFieldToSelect('fb_attribute_code');
        $editfbAttributeData = $editfbAttribute->getData();
        $attributeMapColl = $this->attributeMapFactory->create()->getCollection()
                                 ->addFieldToFilter('fb_attribute',['in'=>$editfbAttributeData])
                                 ->addFieldToSelect('fb_attribute');
        
        $attributeDbMap = array_column($attributeMapColl->getData(), 'fb_attribute');
        
        /* end code for get all editable mapped attributes */
        if(isset($params['attributemap'])){
            $attributeMapArray = $params['attributemap'];
            foreach ($attributeMapArray as $attributeMap) {
                if(in_array($attributeMap['fb_attribute'], $attributeDbMap)){
                    $searchVal = $attributeMap['fb_attribute'];
                    $delKey = array_search ($searchVal, $attributeDbMap);
                    unset($attributeDbMap[$delKey]);
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
            foreach ($attributeDbMap as $removeattr) {
                $attributeMapColl = $this->attributeMapFactory->create()->getCollection()
                                         ->addFieldToFilter('fb_attribute',$removeattr);
                $mappingId = $attributeMapColl->getFirstItem()->getId();
                $this->attributemapRepository->deleteById($mappingId);
            }
            $this->messageManager->addSuccess(__("Attribute Mapping saved successfully"));
        }
        else{
            $this->messageManager->addError(__("Please select attributes for Mapping"));
        }
        
        return $resultRedirect->setPath('*/*/index');
       
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}
