<?php

namespace Meetanshi\Inquiry\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('dealer_inquiry')
        )->addColumn(
            'dealer_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Dealer Id'
        )->addColumn(
            'first_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'First Name'
        )->addColumn(
            'last_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Last Name'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Email ID'
        )->addColumn(
            'company_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Company Name'
        )->addColumn(
            'tax_vat_number',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Tax/VAT Number'
        )->addColumn(
            'address',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null],
            'Address'
        )->addColumn(
            'website',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Website'
        )->addColumn(
            'city',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'City'
        )->addColumn(
            'zip_postal_code',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'ZIP/Postal Code'
        )->addColumn(
            'country',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Country'
        )->addColumn(
            'state',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'State/Province'
        )->addColumn(
            'contact_number',
            Table::TYPE_BIGINT,
            null,
            ['nullable' => true, 'default' => null],
            'Contact Number'
        )->addColumn(
            'business_description',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null],
            'Business Description'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'Created At'
        )->addColumn(
            'files',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Files'
        )->addColumn(
            'extra_field_1',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Extra Field 1'
        )->addColumn(
            'extra_field_2',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Extra field 2'
        )->addColumn(
            'extra_field_3',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Extra Field 3'
        )->addColumn(
            'store_view',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null],
            'Store View'
        )->addColumn(
            'is_customer_created',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'default' => null],
            'Customer Entity ID'
        )->setComment(
            'Dealer Inquiry Form'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
