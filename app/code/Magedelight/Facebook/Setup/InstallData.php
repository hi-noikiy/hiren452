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
namespace Magedelight\Facebook\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magedelight\Facebook\Model\Attributemap;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface {
     
    /**
     * @var eavSetupFactory
     */
    private $eavSetupFactory = null;
    
    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param Product $product
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY,'is_allow_facebook_feed');
        $eavSetup->addAttributeGroup(
                \Magento\Catalog\Model\Product::ENTITY, 'Default', 'Facebook', '30'
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'is_allow_facebook_feed', [
            'type' => 'int',
            'label' => 'Is Allow for Facebook Feed',
            'input' => 'boolean',
            'required' => false,
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
            'group' => 'Facebook',
            'used_in_product_listing' => true,
            'visible_on_front' => false,
            'is_used_for_promo_rules' => true,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'backend' => '',    
            'default' => '0',
            'is_used_in_grid' => true,    
            'is_visible_in_grid' => true,    
            'is_filterable_in_grid' => true,    
                ]
        );
        $setup->startSetup();
        $tableName = $setup->getTable('md_fb_required_attributes');
        if($setup->getConnection()->isTableExists('md_fb_required_attributes')==true){
           $data = [
               [
                   'fb_attribute_code' => 'id',
                   'fb_attribute_label' => 'ID',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_YES,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'availability',
                   'fb_attribute_label' => 'Availability',
                   'poss_value' => 'in stock, out of stock, preorder, available for order,discontinued',
                   'is_required' => Attributemap::IS_REQUIRED_YES,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'description',
                   'fb_attribute_label' => 'Description',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_YES,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'image_link',
                   'fb_attribute_label' => 'Image Link',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_YES,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'link',
                   'fb_attribute_label' => 'Link',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_YES,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => 'It will be dynamic based on product type'
               ],
               [
                   'fb_attribute_code' => 'title',
                   'fb_attribute_label' => 'Title',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_YES,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'price',
                   'fb_attribute_label' => 'Price',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_YES,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'additional_image_link',
                   'fb_attribute_label' => 'Additional Image Link',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => 'It will be product additional image'
               ],
               [
                   'fb_attribute_code' => 'item_group_id',
                   'fb_attribute_label' => 'Item Group Id',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => 'only use in configurable product for group associate products'
               ],
               [
                   'fb_attribute_code' => 'product_type',
                   'fb_attribute_label' => 'Product Type',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => 'category of product'
               ],
               [
                   'fb_attribute_code' => 'sale_price',
                   'fb_attribute_label' => 'Sale Price',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'sale_price_effective_date',
                   'fb_attribute_label' => 'Sale Price Effective Date',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'inventory',
                   'fb_attribute_label' => 'Inventory',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'condition',
                   'fb_attribute_label' => 'Condition',
                   'poss_value' => 'new, refurbished, used',
                   'is_required' => Attributemap::IS_REQUIRED_YES,
                   'editable' => Attributemap::IS_EDITABLE_NO,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'gtin',
                   'fb_attribute_label' => 'GTIN',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_YES,
                   'editable' => Attributemap::IS_EDITABLE_YES,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'mpn',
                   'fb_attribute_label' => 'MPN',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_YES,
                   'editable' => Attributemap::IS_EDITABLE_YES,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'brand',
                   'fb_attribute_label' => 'Brand',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_YES,
                   'editable' => Attributemap::IS_EDITABLE_YES,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'size',
                   'fb_attribute_label' => 'Size',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_YES,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'color',
                   'fb_attribute_label' => 'Color',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_YES,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'gender',
                   'fb_attribute_label' => 'Gender',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_YES,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'pattern',
                   'fb_attribute_label' => 'Pattern',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_YES,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'age_group',
                   'fb_attribute_label' => 'Age Group',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_YES,
                   'comments' => ''
               ],
               [
                   'fb_attribute_code' => 'material',
                   'fb_attribute_label' => 'Material',
                   'poss_value' => '',
                   'is_required' => Attributemap::IS_REQUIRED_NO,
                   'editable' => Attributemap::IS_EDITABLE_YES,
                   'comments' => ''
               ],
           ];
           foreach ($data as $item) {
               $setup->getConnection()->insert($tableName, $item);
           }
       }
        $md_attribute_mapping_table = $setup->getTable('md_attribute_mapping');
        if($setup->getConnection()->isTableExists('md_attribute_mapping')==true){
           $data = [
               [
                   'fb_attribute' => 'id',
                   'mage_attribute' => 'sku',
               ],
               [
                   'fb_attribute' => 'availability',
                   'mage_attribute' => 'is_in_stock',
               ],
               [
                   'fb_attribute' => 'description',
                   'mage_attribute' => 'description',
               ],
               [
                   'fb_attribute' => 'image_link',
                   'mage_attribute' => 'main_img_link',
               ],
               [
                   'fb_attribute' => 'link',
                   'mage_attribute' => 'Dynamic',
               ],
               [
                   'fb_attribute' => 'title',
                   'mage_attribute' => 'name',
               ],
               [
                   'fb_attribute' => 'price',
                   'mage_attribute' => 'price',
               ],
               [
                   'fb_attribute' => 'additional_image_link',
                   'mage_attribute' => 'additional_image',
               ],
               [
                   'fb_attribute' => 'item_group_id',
                   'mage_attribute' => 'Dynamic',
               ],
               [
                   'fb_attribute' => 'product_type',
                   'mage_attribute' => 'category',
               ],
               [
                   'fb_attribute' => 'sale_price',
                   'mage_attribute' => 'special_price',
               ],
               [
                   'fb_attribute' => 'sale_price_effective_date',
                   'mage_attribute' => 'special_date',
               ],
               [
                   'fb_attribute' => 'inventory',
                   'mage_attribute' => 'qty',
               ],
               [
                   'fb_attribute' => 'condition',
                   'mage_attribute' => 'Dynamic',
               ],
           ];
           foreach ($data as $item) {
               $setup->getConnection()->insert($md_attribute_mapping_table, $item);
           }
       } 
       
       $setup->endSetup();
    }
}