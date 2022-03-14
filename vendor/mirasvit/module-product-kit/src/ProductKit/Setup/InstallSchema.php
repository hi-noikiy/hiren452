<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\ProductKit\Api\Data\IndexInterface;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $connection = $installer->getConnection();

        $installer->startSetup();

        $table = $connection->newTable(
            $installer->getTable(KitInterface::TABLE_NAME)
        )->addColumn(
            KitInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            KitInterface::ID
        )->addColumn(
            KitInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            KitInterface::NAME
        )->addColumn(
            KitInterface::LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            KitInterface::LABEL
        )->addColumn(
            KitInterface::IS_ACTIVE,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            KitInterface::IS_ACTIVE
        )->addColumn(
            KitInterface::IS_SMART,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            KitInterface::IS_SMART
        )->addColumn(
            KitInterface::STORE_IDS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            KitInterface::STORE_IDS
        )->addColumn(
            KitInterface::CUSTOMER_GROUP_IDS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            KitInterface::CUSTOMER_GROUP_IDS
        )->addColumn(
            KitInterface::PRIORITY,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            KitInterface::PRIORITY
        )->addColumn(
            KitInterface::ACTIVE_FROM,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            KitInterface::ACTIVE_FROM
        )->addColumn(
            KitInterface::ACTIVE_TO,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            KitInterface::ACTIVE_TO
        )->addColumn(
            KitInterface::STOP_RULES_PROCESSING,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            KitInterface::STOP_RULES_PROCESSING
        )->addColumn(
            KitInterface::BLOCK_TITLE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            KitInterface::BLOCK_TITLE
        )->addColumn(
            KitInterface::PRICE_PATTERN,
            Table::TYPE_TEXT,
            25,
            ['nullable' => true],
            KitInterface::PRICE_PATTERN
        )->addColumn(
            KitInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            KitInterface::CREATED_AT
        );

        $connection->dropTable($setup->getTable(KitInterface::TABLE_NAME));
        $connection->createTable($table);

        $table = $connection->newTable(
            $installer->getTable(KitItemInterface::TABLE_NAME)
        )->addColumn(
            KitItemInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            KitItemInterface::ID
        )->addColumn(
            KitItemInterface::KIT_ID,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            KitItemInterface::KIT_ID
        )->addColumn(
            KitItemInterface::PRODUCT_ID,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            KitItemInterface::PRODUCT_ID
        )->addColumn(
            KitItemInterface::POSITION,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => 1],
            KitItemInterface::POSITION
        )->addColumn(
            KitItemInterface::IS_OPTIONAL,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            KitItemInterface::IS_OPTIONAL
        )->addColumn(
            KitItemInterface::QTY,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => 1],
            KitItemInterface::QTY
        )->addColumn(
            KitItemInterface::DISCOUNT_TYPE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            KitItemInterface::DISCOUNT_TYPE
        )->addColumn(
            KitItemInterface::DISCOUNT_AMOUNT,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => 0],
            KitItemInterface::DISCOUNT_AMOUNT
        )->addColumn(
            KitItemInterface::CONDITIONS,
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null],
            KitItemInterface::CONDITIONS
        );

        $connection->dropTable($setup->getTable(KitItemInterface::TABLE_NAME));
        $connection->createTable($table);

        $table = $connection->newTable(
            $installer->getTable(IndexInterface::TABLE_NAME)
        )->addColumn(
            IndexInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            IndexInterface::ID
        )->addColumn(
            IndexInterface::KIT_ID,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            IndexInterface::KIT_ID
        )->addColumn(
            IndexInterface::PRODUCT_ID,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            IndexInterface::PRODUCT_ID
        )->addColumn(
            IndexInterface::ITEM_ID,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            IndexInterface::ITEM_ID
        )->addColumn(
            IndexInterface::POSITION,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            IndexInterface::POSITION
        )->addColumn(
            IndexInterface::IS_OPTIONAL,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            IndexInterface::IS_OPTIONAL
        );

        $connection->dropTable($setup->getTable(IndexInterface::TABLE_NAME));
        $connection->createTable($table);
    }
}
