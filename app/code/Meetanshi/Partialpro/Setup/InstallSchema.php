<?php

namespace Meetanshi\Partialpro\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()->newTable($installer->getTable('meetanshi_partial_payment'))
            ->addColumn(
                'partial_payment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Primary key'
            )
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                60,
                ['nullable' => false],
                'Order Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Store Id'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false],
                'Store Customer Id'
            )->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                60,
                ['unsigned' => true, 'nullable' => false],
                'customer Name'
            )
            ->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                60,
                ['unsigned' => true, 'nullable' => false],
                'customer Email'
            )
            ->addColumn(
                'order_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Order Amount'
            )
            ->addColumn(
                'paid_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Paid Amount'
            )
            ->addColumn(
                'remaining_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Remaining Amount'
            )
            ->addColumn(
                'total_installments',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => true],
                'Total Installments'
            )
            ->addColumn(
                'paid_installments',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => true],
                'Paid Installments'
            )
            ->addColumn(
                'remaining_installments',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => true],
                'Remaining Installments'
            )
            ->addColumn(
                'payment_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['unsigned' => true, 'nullable' => false],
                'Payment Status'
            )
            ->addColumn(
                'created_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )
            ->addColumn(
                'currency_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'currency code'
            )
            ->addColumn(
                'surcharge_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Surcharge Amount'
            )
            ->addColumn('auto_capture_profile_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Auto Capture Customer Profile Id'
            )
            ->addColumn('auto_capture_payment_profile_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Auto Capture Customer Payment Profile Id');


        $installer->getConnection()->createTable($table);

        $installer->getConnection()->addIndex(
            $installer->getTable('meetanshi_partial_payment'),
            $setup->getIdxName(
                $installer->getTable('meetanshi_partial_payment'),
                [ 'customer_name', 'customer_email'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['customer_name', 'customer_email'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
        );


        $table = $installer->getConnection()->newTable($installer->getTable('meetanshi_installment_summary'))
            ->addColumn(
                'installment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                8,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Primary key'
            )
            ->addColumn(
                'partial_payment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Partial Payment Id'
            )
            ->addColumn(
                'installment_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Installment Amount'
            )
            ->addColumn(
                'installment_due_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Installment Due Date'
            )
            ->addColumn(
                'installment_paid_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Installment Paid Date'
            )
            ->addColumn(
                'installment_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['unsigned' => true, 'nullable' => false],
                'Installment Status'
            )
            ->addColumn(
                'payment_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                60,
                ['unsigned' => true, 'nullable' => false],
                'Payment Method'
            )
            ->addColumn(
                'transaction_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                60,
                ['unsigned' => true, 'nullable' => false],
                'Transaction Id'
            )
            ->addColumn(
                'reminder_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                60,
                ['unsigned' => true, 'nullable' => false],
                'Reminder Email'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'meetanshi_installment_summary',
                    'partial_payment_id',
                    'meetanshi_partial_payment',
                    'partial_payment_id'
                ),
                'partial_payment_id',
                $installer->getTable('meetanshi_partial_payment'),
                'partial_payment_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
