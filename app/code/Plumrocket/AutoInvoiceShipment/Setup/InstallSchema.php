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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer  = $setup;
        $connection = $installer->getConnection();

        $installer->startSetup();

        $tableName = $installer->getTable('pl_autoinvoiceshipment_invoicerules');
        $table = $connection
            ->newTable($tableName)
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Invoice Rule Id'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Invoice Rule Title'
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1'],
                'Is Invoice Rule Active'
            )

            ->addColumn(
                'create_invoice',
                Table::TYPE_INTEGER,
                null,
                [],
                'Create Invoice After'
            )
            ->addColumn(
                'capture_amount',
                Table::TYPE_INTEGER,
                null,
                [],
                'Capture Amount Type'
            )

            ->addColumn(
                'websites',
                Table::TYPE_TEXT,
                1000,
                ['nullable' => false],
                'Enable on Websites'
            )
            ->addColumn(
                'customer_groups',
                Table::TYPE_TEXT,
                1000,
                ['nullable' => false],
                'Enable for Customer Groups'
            )

            ->addColumn(
                'comment',
                Table::TYPE_TEXT,
                '2M',
                [],
                'Comment'
            )
            ->addColumn(
                'comment_to_email',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Append Comment To Shipment Email'
            )
            ->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                '2M',
                [],
                'Conditions Serialized'
            )
            ->setComment('Auto Invoice & Shipment');
        $connection->createTable($table);

        $tableName = $installer->getTable('pl_autoinvoiceshipment_shipmentrules');
        $table = $connection
            ->newTable($tableName)
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Invoice Rule Id'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Invoice Rule Title'
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1'],
                'Is Invoice Rule Active'
            )
            ->addColumn(
                'create_shipment',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '2'],
                'Create Shipment After'
            )

            ->addColumn(
                'websites',
                Table::TYPE_TEXT,
                1000,
                ['nullable' => false],
                'Enable on Websites'
            )
            ->addColumn(
                'customer_groups',
                Table::TYPE_TEXT,
                1000,
                ['nullable' => false],
                'Enable for Customer Groups'
            )

            ->addColumn(
                'comment',
                Table::TYPE_TEXT,
                '2M',
                [],
                'Comment'
            )
            ->addColumn(
                'comment_to_email',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Append Comment To Shipment Email'
            )
            ->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                '2M',
                [],
                'Conditions Serialized'
            )
            ->setComment('Auto Invoice & Shipment');
        $connection->createTable($table);

        $installer->endSetup();
    }
}
