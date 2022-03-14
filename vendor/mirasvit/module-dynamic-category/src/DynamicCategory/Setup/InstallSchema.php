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
 * @package   mirasvit/module-dynamic-category
 * @version   1.0.17
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



declare(strict_types=1);

namespace Mirasvit\DynamicCategory\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\DynamicCategory\Api\Data\DynamicCategoryInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $installer = $setup;

        $connection = $installer->getConnection();

        $installer->startSetup();

        $table = $connection->newTable(
            $installer->getTable(DynamicCategoryInterface::TABLE_NAME)
        )->addColumn(
            DynamicCategoryInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            DynamicCategoryInterface::ID
        )->addColumn(
            DynamicCategoryInterface::CATEGORY_ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            DynamicCategoryInterface::CATEGORY_ID
        )->addColumn(
            DynamicCategoryInterface::CONDITIONS_SERIALIZED,
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null],
            DynamicCategoryInterface::CONDITIONS_SERIALIZED
        )->addColumn(
            DynamicCategoryInterface::IS_ACTIVE,
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => 0],
            DynamicCategoryInterface::IS_ACTIVE
        );

        $connection->dropTable($setup->getTable(DynamicCategoryInterface::TABLE_NAME));
        $connection->createTable($table);
    }
}
