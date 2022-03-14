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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    
    /**
     * 
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /*
         * create table md_attribute_mapping
         */
        $md_attribute_mapping_table = $installer->getConnection()->newTable(
            $installer->getTable('md_attribute_mapping')
        )->addColumn(
            'mapping_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Attribute Mapping Id'
        )->addColumn(
            'mage_attribute',
             \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false],
            'Product Attribute'
        )->addColumn(
            'fb_attribute',
             \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false],
            'Facebook Attribute'
        );
        $installer->getConnection()->createTable($md_attribute_mapping_table);
        
        /*
         * create table md_fb_products
         */
        $md_fb_products_table = $installer->getConnection()->newTable(
            $installer->getTable('md_fb_products')
        )->addColumn(
            'fb_product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Facebook Product Id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Product Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Facebook Product Status'
        );
        $installer->getConnection()->createTable($md_fb_products_table);
        
        /*
         * create table md_cron_schedule_history
         */
        $md_cron_schedule_history_table = $installer->getConnection()->newTable(
            $installer->getTable('md_cron_schedule_history')
        )->addColumn(
            'md_cron_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Cron Id'
        )->addColumn(
            'cron_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'cron date'
        )->addColumn(
            'message',
             \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '',
            ['nullable' => false],
            'Message'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Type'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Status'
        );
        $installer->getConnection()->createTable($md_cron_schedule_history_table);
        
         /*
         * create table md_fb_required_attributes
         */
        $md_fb_required_attributes_table = $installer->getConnection()->newTable(
            $installer->getTable('md_fb_required_attributes')
        )->addColumn(
            'fb_attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Required Attribute Id'
        )->addColumn(
            'fb_attribute_code',
             \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false],
            'FB Attribute Code'
        )->addColumn(
            'fb_attribute_label',
             \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false],
            'Facebook Attribute Label'
        )->addColumn(
            'poss_value',
             \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false],
            'Possible Value'
        )->addColumn(
            'is_required',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Status'
        )->addColumn(
            'editable',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Editable'
        )->addColumn(
            'comments',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '',
            ['nullable' => false],
            'Comments'
        );
        $installer->getConnection()->createTable($md_fb_required_attributes_table);
        $installer->endSetup();
    }
}
