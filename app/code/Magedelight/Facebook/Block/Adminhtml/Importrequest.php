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

class Importrequest extends \Magento\Backend\Block\Template {
    
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
        $this->setChild('attributemap_import_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => __('Import'),
                    'class' => 'import icon-btn primary',
                    'on_click' => 'importCsv()',
                ))
        );
        $this->setChild('attributemap_back_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => __('Back'),
                    'class' => 'action- scalable back',
                    'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
                ))
        );
        parent::_prepareLayout();
    }
    public function getAttributeMapImportButtonHtml()
    {
        return $this->getChildHtml('attributemap_import_button');
    }
    public function getAttributeMapBackButtonHtml()
    {
        return $this->getChildHtml('attributemap_back_button');
    }
    public function getAttributeMapAddButtonHtml()
    {
        return $this->getChildHtml('attributemap_add_button');
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
            $attr_groups[] = $items->getData();
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
    
     /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }
}
