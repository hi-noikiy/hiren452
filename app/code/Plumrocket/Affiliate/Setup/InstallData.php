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

use Magento\Eav\Setup\EavSetupFactory;
use Plumrocket\Affiliate\Model\Affiliate;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Install Data
     * @param  ModuleDataSetupInterface $setup
     * @param  ModuleContextInterface   $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
        $connection = $setup->getConnection();

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'affiliate_tradedoubler_groupid',
            [
                'type' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'TradeDoubler Group ID',
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
                'position' => 250
            ]
        );

        $affiliates = [
            [
                'id' => 1,
                'key' => 'custom',
                'name' => 'Custom',
                'order' => 500
            ],
            [
                'id' => 2,
                'key' => 'avantLink',
                'name' => 'AvantLink',
                'order' => 50
            ],
            [
                'id' => Affiliate\Hasoffers::TYPE_ID,
                'key' => 'hasoffers',
                'name' => 'HasOffers',
                'order' => 40
            ],
            [
                'id' => 4,
                'key' => 'googleEcommerceTracking',
                'name' => 'Google Analytics Ecommerce Tracking',
                'order' => 80
            ],
            [
                'id' => 5,
                'key' => 'shareasale',
                'name' => 'ShareASale',
                'order' => 20
            ],
            [
                'id' => 6,
                'key' => 'linkshare',
                'name' => 'LinkShare',
                'order' => 10
            ],
            [
                'id' => Affiliate\CommissionJunction::TYPE_ID,
                'key' => 'commissionJunction',
                'name' => 'Commission Junction',
                'order' => 30
            ],
            [
                'id' => 8,
                'key' => 'chango',
                'name' => 'Chango',
                'order' => 70
            ],
            [
                'id' => 9,
                'key' => 'shopzilla',
                'name' => 'Shopzilla',
                'order' => 60
            ],
            [
                'id' => 10,
                'key' => 'ebayEnterprise',
                'name' => 'eBay Enterprise',
                'order' => 100
            ],
            [
                'id' => 11,
                'key' => 'affiliateWindow',
                'name' => 'AWIN',
                'order' => 90
            ],
            [
                'id' => Affiliate\Tradedoubler::TYPE_ID,
                'key' => 'tradedoubler',
                'name' => 'Tradedoubler',
                'order' => 110
            ],
            [
                'id' => 13,
                'key' => 'linkconnector',
                'name' => 'Linkconnector',
                'order' => 160
            ],
            [
                'id' => 14,
                'key' => 'zanox',
                'name' => 'Zanox',
                'order' => 120
            ],
            [
                'id' => 15,
                'key' => 'webGains',
                'name' => 'WebGains',
                'order' => 130
            ],
            [
                'id' => 16,
                'key' => 'performanceHorizon',
                'name' => 'PerformanceHorizon',
                'order' => 140
            ],
            [
                'id' => 17,
                'key' => 'impactRadius',
                'name' => 'ImpactRadius',
                'order' => 150
            ],
        ];

        $connection->insertMultiple($setup->getTable('plumrocket_affiliate_type'), $affiliates);

        $includeons = [
            [
                'id' => 1,
                'key' => 'all',
                'name' => 'All Pages',
                'order' => 10
            ],
            [
                'id' => 2,
                'key' => 'registration_success_pages',
                'name' => 'Registration Success Pages',
                'order' => 20
            ],
            [
                'id' => 3,
                'key' => 'login_success_pages',
                'name' => 'Login Success Pages',
                'order' => 30
            ],
            [
                'id' => 4,
                'key' => 'home_page',
                'name' => 'Home Page',
                'order' => 40
            ],
            [
                'id' => 5,
                'key' => 'product_page',
                'name' => 'Product Page',
                'order' => 50
            ],
            [
                'id' => 6,
                'key' => 'category_page',
                'name' => 'Category Page',
                'order' => 60
            ],
            [
                'id' => 7,
                'key' => 'cart_page',
                'name' => 'Shoping Cart Page',
                'order' => 70
            ],
            [
                'id' => 8,
                'key' => 'one_page_chackout',
                'name' => 'One Page Checkout',
                'order' => 80
            ],
            [
                'id' => 9,
                'key' => 'checkout_success',
                'name' => 'Order Success Page',
                'order' => 90
            ],
        ];

        $connection->insertMultiple($setup->getTable('plumrocket_affiliate_includeon'), $includeons);
    }
}
