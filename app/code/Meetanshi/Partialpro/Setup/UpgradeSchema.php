<?php

namespace Meetanshi\Partialpro\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;

class UpgradeSchema implements UpgradeSchemaInterface
{
    protected $resourceConfig;

    public function __construct(
        ConfigInterface $resourceConfig)
    {
        $this->resourceConfig = $resourceConfig;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $quoteAddressTable = 'quote_address';
        $quoteTable = 'quote';
        $orderTable = 'sales_order';
        $invoiceTable = 'sales_invoice';
        $creditmemoTable = 'sales_creditmemo';
        $quoteItem = 'quote_item';
        $orderItem = 'sales_order_item';
        $quoteAddressItem = 'quote_address_item';


        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'partial_installment_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Installment fee'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'partial_pay_now',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay now'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'partial_pay_later',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay later'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'partial_max_installment',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => '2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Max Installment'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'partial_installment_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Installment fee'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'partial_pay_now',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay now'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'partial_pay_later',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay later'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'partial_max_installment',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => '2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Max Installment'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'partial_installment_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Installment fee'

                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'partial_pay_now',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay now'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'partial_pay_later',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay later'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'partial_max_installment',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => '2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Max Installment'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'partial_installment_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Installment fee'

                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'partial_pay_now',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay now'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'partial_pay_later',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay later'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'partial_max_installment',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => '2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Max Installment'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'partial_installment_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Installment fee'

                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'partial_pay_now',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay now'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'partial_pay_later',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay later'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'partial_max_installment',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => '2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Max Installment'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteItem),
                'partial_apply',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => '1',
                    'default' => 0,
                    'nullable' => true,
                    'comment' => 'Is Partial Payment apply'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderItem),
                'partial_apply',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => '1',
                    'default' => 0,
                    'nullable' => true,
                    'comment' => 'Is Partial Payment apply'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteItem),
                'partial_installment_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0,
                    'nullable' => true,
                    'comment' => 'Partial Payment installment fee'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteItem),
                'partial_installment_no',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => '2',
                    'default' => 0,
                    'nullable' => true,
                    'comment' => 'Partial Payment installment number'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteItem),
                'partial_pay_now',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay now'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteItem),
                'partial_pay_later',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Partial Payment Amount pay later'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderItem),
                'partial_installment_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Partial Payment installment fee'
                ]
            );


        if (version_compare($context->getVersion(), '1.0.2', '<')) {


            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($quoteTable),
                    'partial_order',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => '3k',
                        'default' => '',
                        'nullable' => true,
                        'comment' => 'multi shipping order info'
                    ]
                );


            //quote address item

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($quoteAddressItem),
                    'partial_apply',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length' => '1',
                        'default' => 0,
                        'nullable' => true,
                        'comment' => 'Is Partial Payment apply'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($quoteAddressItem),
                    'partial_installment_no',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length' => '2',
                        'default' => 0,
                        'nullable' => true,
                        'comment' => 'Partial Payment installment number'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($quoteAddressItem),
                    'partial_pay_now',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' => 'Partial Payment Amount pay now'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($quoteAddressItem),
                    'partial_pay_later',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' => 'Partial Payment Amount pay later'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($quoteAddressItem),
                    'partial_installment_fee',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => true,
                        'comment' => 'Partial Payment installment fee'
                    ]
                );

            $this->resourceConfig->saveConfig(
                'checkout/cart/delete_quote_after',
                '3000',
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                Store::DEFAULT_STORE_ID
            );
        }

        $setup->endSetup();
    }
}
