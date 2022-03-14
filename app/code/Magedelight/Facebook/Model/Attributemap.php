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
namespace Magedelight\Facebook\Model;

use Magento\Framework\Model\AbstractModel;
use Magedelight\Facebook\Api\Data\AttributemapInterface;

class Attributemap extends AbstractModel implements AttributemapInterface{
    
    const IS_REQUIRED_YES  = 1;
    const IS_REQUIRED_NO  = 2;
    const IS_EDITABLE_YES  = 1;
    const IS_EDITABLE_NO  = 2;
    
    const AVAILABILITY = 'availability';
    const IMAGELINK = 'image_link';
    const LINK = 'link';
    const ITEMGROUPID = 'item_group_id';
    const PRODUCTTYPE = 'product_type';
    const SALEPRICE = 'sale_price';
    const SALEPRICE_EFFECTIVE = 'sale_price_effective_date';
    const INVENTORY = 'inventory';
    
    const IS_IN_STOCK = 'is_in_stock';
    const MAINIMAGELINK = 'main_img_link';
    const DYNAMIC = 'Dynamic';
    const CATEGORY = 'category';
    const SPECIALPRICE = 'special_price';
    const SPECIALDATE = 'special_date';
    const ADDITIONALIMG = 'additional_image';
    const QTY = 'qty';
   
    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    public function _construct()
    {
        $this->_init('Magedelight\Facebook\Model\ResourceModel\Attributemap');
    }
    
    /**
     * Get ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(AttributemapInterface::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Magedelight\Facebook\Api\Data\AttributemapInterface
     */
    public function setId($id)
    {
       $this->setData(AttributemapInterface::ENTITY_ID, $id);
       return $this; 
    }        
          
    /**
     * Get MageAttribute.
     *
     * @return string|null
     */
    public function getMageAttribute()
    {
        return $this->getData(AttributemapInterface::MAGE_ATTRIBUTE);
    }

    /**
     * Set MageAttribute.
     *
     * @param int $mageAttribute
     *
     * @return \Magedelight\Facebook\Api\Data\AttributemapInterface
     */
    public function setMageAttribute($mageAttribute)
    {
        $this->setData(AttributemapInterface::MAGE_ATTRIBUTE, $mageAttribute);
       return $this; 
    }
    
    /**
     * Get FbAttribute.
     *
     * @return string|null
     */
    public function getFbAttribute()
    {
        return $this->getData(AttributemapInterface::FB_ATTRIBUTE);
    }

    /**
     * Set FbAttribute.
     *
     * @param string|null $fbattribute
     *
     * @return \Magedelight\Facebook\Api\Data\AttributemapInterface
     */
    public function setFbAttribute($fbattribute)
    {
       $this->setData(AttributemapInterface::FB_ATTRIBUTE, $fbattribute);
       return $this; 
    }
}
