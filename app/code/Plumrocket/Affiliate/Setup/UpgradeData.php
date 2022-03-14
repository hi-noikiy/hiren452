<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Set
     */
    private $attributeSet;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\Entity\Attribute\Set $attributeSet
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Entity\Attribute\Set $attributeSet
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->attributeSet = $attributeSet;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '2.1.0', '<')) {

            /** @var EavSetup $eavSetup */
            $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'affiliate_aw_commission_group',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'AWIN Commission Group',
                    'input' => 'text',
                    'group' => 'Affiliate Programs',
                    'class' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => '',
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'position' => 260
                ]
            );

            // Add new networks.
            $affiliates = [
                [
                    'id' => 18,
                    'key' => 'sidecar',
                    'name' => 'Sidecar',
                    'order' => 160
                ],
                [
                    'id' => 19,
                    'key' => 'flexoffers',
                    'name' => 'FlexOffers',
                    'order' => 170
                ]
            ];
            $connection->insertMultiple($setup->getTable('plumrocket_affiliate_type'), $affiliates);
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {

            // Add new networks.
            $affiliates = [
                [
                    'id' => \Plumrocket\Affiliate\Model\Affiliate\Pepperjam::TYPE_ID,
                    'key' => 'pepperjam',
                    'name' => 'Pepperjam',
                    'order' => 180
                ],
                [
                    'id' => 21,
                    'key' => 'criteo',
                    'name' => 'Criteo',
                    'order' => 190
                ],
                [
                    'id' => 22,
                    'key' => 'roiTracker',
                    'name' => 'eBay Commerce Network',
                    'order' => 200
                ]
            ];
            $connection->insertMultiple($setup->getTable('plumrocket_affiliate_type'), $affiliates);
        }

        if (version_compare($context->getVersion(), '2.3.0', '<')) {

            // Add new networks.
            $affiliates =  [
                'id' => 23,
                'key' => 'affilinet',
                'name' => 'Affilinet',
                'order' => 210
            ];
            $connection->insert($setup->getTable('plumrocket_affiliate_type'), $affiliates);

            $includeons = [
                'id' => 10,
                'key' => 'catalogsearch_result_page',
                'name' => 'Search Result Page',
                'order' => 100
            ];

            $connection->insert($setup->getTable('plumrocket_affiliate_includeon'), $includeons);
        }

        if (version_compare($context->getVersion(), '2.3.2', '<')) {
            if (!isset($eavSetup)) {
                /** @var EavSetup $eavSetup */
                $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
            }

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'affiliate_webgains_eventid',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'WebGains Event ID',
                    'input' => 'text',
                    'group' => 'Affiliate Programs',
                    'class' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => '',
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'position' => 270
                ]
            );

            $sets = $this->attributeSet
                ->getResourceCollection()
                ->addFilter('entity_type_id', \Magento\Catalog\Model\Category::ENTITY);

            foreach ($sets as $set) {
                $eavSetup->addAttributeGroup(
                    \Magento\Catalog\Model\Category::ENTITY,
                    $set->getData('attribute_set_id'),
                    'Affiliate Programs',
                    50
                );
            }

            $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
            $attributeSetId   = $eavSetup->getDefaultAttributeSetId($entityTypeId);
            $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Affiliate Programs');

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'affiliate_webgains_eventid',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'WebGains Event ID',
                    'input' => 'text',
                    'group' => 'Affiliate Programs',
                    'class' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => '',
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'position' => 270
                ]
            );

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $attributeGroupId,
                'affiliate_webgains_eventid',
                '270'
            );
        }

        if (version_compare($context->getVersion(), '2.4.0', '<')) {
            $affiliates = [
                'id' => 24,
                'key' => 'affiliateFuture',
                'name' => 'AffiliateFuture',
                'order' => 220
            ];
            $connection->insert($setup->getTable('plumrocket_affiliate_type'), $affiliates);
        }

        if (version_compare($context->getVersion(), '2.5.0', '<')) {
            $affiliates = [
                'id' => 25,
                'key' => 'powerReviews',
                'name' => 'PowerReviews',
                'order' => 230
            ];
            $connection->insert($setup->getTable('plumrocket_affiliate_type'), $affiliates);
        }

        if (version_compare($context->getVersion(), '2.6.0', '<')) {
            if (!isset($eavSetup)) {
                /** @var EavSetup $eavSetup */
                $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
            }
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'affiliate_aw_commission_group',
                'label',
                'AWIN Commission Group'
            );

            $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
            $attributeSetId   = $eavSetup->getDefaultAttributeSetId($entityTypeId);
            $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Affiliate Programs');

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'affiliate_aw_commission_group',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'AWIN Commission Group',
                    'input' => 'text',
                    'group' => 'Affiliate Programs',
                    'class' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => '',
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'position' => 280
                ]
            );

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $attributeGroupId,
                'affiliate_aw_commission_group',
                '280'
            );
        }

        $setup->endSetup();
    }
}
