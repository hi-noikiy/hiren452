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
namespace Magedelight\Facebook\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magedelight\Facebook\Model\AttributemapFactory;
use Magedelight\Facebook\Model\FbattributesFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magedelight\Facebook\Model\Attributemap;

class Attributemapper extends \Magento\Backend\Block\Template {
    
    /**
     *
     * @var AttributemapFactory 
     */
    protected $attributemapFactory;
    
    /**
     *
     * @var FbattributesFactory 
     */
    protected $fbattributesFactory;
    
    /**
     * 
     * @param Context $context
     * @param AttributemapFactory $attributemapFactory
     * @param FbattributesFactory $fbattributesFactory
     */
    public function __construct(
        Context $context,
        AttributemapFactory $attributemapFactory,
        FbattributesFactory $fbattributesFactory,
        CollectionFactory $collectionFactory    
    ) {
        $this->attributemapFactory = $attributemapFactory;
        $this->fbattributesFactory = $fbattributesFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('attributemap_add_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => __('Add Attribute Mapping'),
                    'class' => 'add',
                    'id' => 'add_new_map',
                    'on_click' => 'addItem()',
                ))
        );
        $this->setChild('attributemap_delete_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => __('Delete'),
                    'class' => 'delete icon-btn attribute-del',
                    'on_click' => 'deleteItem(this)',
                ))
        );
        $this->setChild('attributemap_save_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => __('Save'),
                    'class' => 'save icon-btn primary',
                    'on_click' => 'saveAttrMap()',
                ))
        );
        $this->setChild('attributemap_import_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => __('Import Csv'),
                    'class' => 'import icon-btn primary',
                    'on_click' => 'importCsv()',
                ))
        );
        parent::_prepareLayout();
    }
    public function getAttributeMapAddButtonHtml()
    {
        return $this->getChildHtml('attributemap_add_button');
    }

    public function getAttributeMapDeleteButtonHtml()
    {
        
        return $this->getChildHtml('attributemap_delete_button');
    }
    public function getAttributeMapSaveButtonHtml()
    {
        return $this->getChildHtml('attributemap_save_button');
    }
    public function getAttributeMapImportButtonHtml()
    {
        return $this->getChildHtml('attributemap_import_button');
    }
    
    public function getAttributeMap()
    {
        $attributeMapColl = $this->getAttributeMapColl();
        $attributeMap = $attributeMapColl->getData();
        return $attributeMap;
    }
    
    private function getAttributeMapColl()
    {
        return $this->attributemapFactory->create()
                                ->getCollection();
    }

    public function getNoEditableAttributeMap()
    {
        $NoEditfbAttribute = $this->getFbAttrColl()
                                ->addFieldToFilter('editable',\Magedelight\Facebook\Model\Attributemap::IS_EDITABLE_NO)
                                ->addFieldToSelect('fb_attribute_code');
        $NoEditfbAttributeData = $NoEditfbAttribute->getData();
        $attributeMapColl = $this->getAttributeMapColl()
                                 ->addFieldToFilter('fb_attribute',['in'=>$NoEditfbAttributeData]);
        $attributeMap = $attributeMapColl->getData();
        return $attributeMap;
    }
    
    public function getFbAttributes()
    {
        $fbAttributeColl = $this->getFbAttrColl();
        $fbAttribute = $fbAttributeColl->getData();
        return $fbAttribute;
    }
    
    private function getFbAttrColl()
    {
        return $this->fbattributesFactory->create()
                                ->getCollection();
    }
    
    public function getAttributes()
    {
        $collection = $this->collectionFactory->create();
        $attr_groups = array();
        foreach ($collection as $items) {
            if($items->getFrontendLabel()!=null || $items->getFrontendLabel()!=''){
               $attr_groups[] = $items->getData(); 
            }
            
        }
        return $attr_groups; 
    }
    
    public function getDynamicAttr()
    {
        $dynamicAttr = [];
        $dynamicAttr[Attributemap::IS_IN_STOCK] = Attributemap::IS_IN_STOCK;
        $dynamicAttr[Attributemap::MAINIMAGELINK] = Attributemap::MAINIMAGELINK;
        $dynamicAttr[Attributemap::DYNAMIC] = Attributemap::DYNAMIC;
        $dynamicAttr[Attributemap::CATEGORY] = Attributemap::CATEGORY;
        $dynamicAttr[Attributemap::SPECIALPRICE] = Attributemap::SPECIALPRICE;
        $dynamicAttr[Attributemap::SPECIALDATE] = Attributemap::SPECIALDATE;
        $dynamicAttr[Attributemap::ADDITIONALIMG] = Attributemap::ADDITIONALIMG;
        $dynamicAttr[Attributemap::QTY] = Attributemap::QTY;
        return $dynamicAttr;
    }
    
    public function getOptionalVal()
    {
        return ['brand','mpn','gtin'];
    }
}
