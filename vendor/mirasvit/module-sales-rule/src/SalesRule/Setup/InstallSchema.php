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
 * @package   mirasvit/module-sales-rule
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SalesRule\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\SalesRule\Api\Data\RuleInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable(RuleInterface::TABLE_NAME)
        )->addColumn(
            RuleInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'identity' => true, 'primary' => true],
            RuleInterface::ID
        )->addColumn(
            RuleInterface::PARENT_ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            RuleInterface::PARENT_ID
        )->addColumn(
            RuleInterface::COUPON_SUCCESS_MESSAGE,
            Table::TYPE_TEXT,
            65536,
            ['nullable' => true],
            RuleInterface::COUPON_SUCCESS_MESSAGE
        )->addColumn(
            RuleInterface::COUPON_ERROR_MESSAGE,
            Table::TYPE_TEXT,
            65536,
            ['nullable' => true],
            RuleInterface::COUPON_ERROR_MESSAGE
        )->addColumn(
            RuleInterface::SKIP_CONDITION,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            RuleInterface::SKIP_CONDITION
        );
        $installer->getConnection()->createTable($table);
    }
}
