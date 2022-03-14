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
 * @package   Plumrocket_AutoInvoiceShipment
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AutoInvoiceShipment\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $tables = [
                'pl_autoinvoiceshipment_invoicerules',
                'pl_autoinvoiceshipment_shipmentrules'
            ];
            foreach ($tables as $table) {
                $tableName = $setup->getTable($table);
                if ($setup->getConnection()->isTableExists($tableName) == true) {
                    $connection->addColumn(
                        $tableName,
                        'rules_priority',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'length' => 11,
                            'unsigned' => true,
                            'nullable' => false,
                            'comment' => 'Rules Priority'
                        ]
                    );
                }
            }
        }

        $setup->endSetup();
    }
}
