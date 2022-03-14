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



namespace Mirasvit\ProductKit\Setup\UpgradeSchema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Mirasvit\ProductKit\Api\Data\IndexInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;

class UpgradeSchema101 implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();

        $connection->addColumn($setup->getTable(KitItemInterface::TABLE_NAME), KitItemInterface::IS_PRIMARY, [
            'type'    => Table::TYPE_INTEGER,
            'length'  => 1,
            'default' => 0,
            'comment' => KitItemInterface::IS_PRIMARY,
        ]);

        $connection->addColumn($setup->getTable(IndexInterface::TABLE_NAME), IndexInterface::IS_PRIMARY, [
            'type'    => Table::TYPE_INTEGER,
            'length'  => 1,
            'default' => 0,
            'comment' => IndexInterface::IS_PRIMARY,
        ]);
    }
}
