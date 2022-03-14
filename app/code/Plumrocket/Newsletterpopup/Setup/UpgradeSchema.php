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
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        try {
            /* 1.2.0 */
            $connection = $setup->getConnection();
            $tableName = $setup->getTable('newsletter_subscriber');
            if ($connection->isTableExists($tableName) == true) {
                $connection->addColumn(
                    $tableName,
                    'subscriber_firstname',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber Firstname']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_middlename',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber Middlename']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_lastname',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber Lastname']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_suffix',
                    ['type' => Table::TYPE_TEXT, 'length' => 32, 'nullable' => true, 'comment' => 'Subscriber Suffix']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_dob',
                    ['type' => Table::TYPE_DATE, 'nullable' => true, 'comment' => 'Subscriber Dob']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_gender',
                    ['type' => Table::TYPE_INTEGER, 'length' => 11, 'nullable' => true, 'comment' => 'Subscriber Gender']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_taxvat',
                    ['type' => Table::TYPE_TEXT, 'length' => 128, 'nullable' => true, 'comment' => 'Subscriber Taxvat']
                );

                /* 2.0.0 */
                $connection->addColumn(
                    $tableName,
                    'subscriber_prefix',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber Prefix']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_telephone',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber Telephone']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_fax',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber Fax']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_company',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber Company']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_street',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber Street']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_city',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber City']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_country_id',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber Country ID']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_region',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber Region']
                );
                $connection->addColumn(
                    $tableName,
                    'subscriber_postcode',
                    ['type' => Table::TYPE_TEXT, 'length' => 150, 'nullable' => true, 'comment' => 'Subscriber Postcode']
                );
            }

            /**
             * Version 3.1.0
             */
            if (version_compare($context->getVersion(), '3.1.0', '<')) {
                /**
                 * Add expiration_date column into plumrocket_newsletterpopup_popups
                 */
                $tableName = $setup->getTable('plumrocket_newsletterpopup_popups');

                if ($connection->isTableExists($tableName) == true) {
                    $connection->addColumn($tableName,
                        'coupon_expiration_time', [
                            'type' => Table::TYPE_TEXT,
                            'length' => 32,
                            'nullable' => true,
                            'comment' => 'Coupon Expiration Time',
                            'after' => 'end_date',
                        ]
                    );

                    $connection->modifyColumn(
                        $tableName,
                        'success_page',
                        [
                            'type' => Table::TYPE_TEXT,
                            'length' => 255,
                        ]
                    );

                    $connection->modifyColumn(
                        $tableName,
                        'cookie_time_frame',
                        [
                            'type' => Table::TYPE_TEXT,
                            'length' => 32,
                            'nullable' => true,
                            'default' => '30,0,0,0',
                        ]
                    );
                }

                /**
                 * Add expired_on column into salesrule_coupon
                 */
                $tableName = $setup->getTable('salesrule_coupon');

                if ($connection->isTableExists($tableName) == true) {
                    $connection->addColumn($tableName,
                        'np_expiration_date', [
                            'type' => Table::TYPE_DATETIME,
                            'nullable' => true,
                            'comment' => 'Expiration Date By Newsletterpopup',
                            'after' => 'expiration_date',
                        ]
                    );
                }
            }

            /**
             * Version 3.1.1
             */
            if (version_compare($context->getVersion(), '3.1.1', '<')) {
                /**
                 * Increase length of column "label" in plumrocket_newsletterpopup_form_fields
                 */
                $connection->modifyColumn(
                    $setup->getTable('plumrocket_newsletterpopup_form_fields'),
                    'label',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 1000,
                    ]
                );
            }

            /**
             * Version 3.3.0
             */
            if (version_compare($context->getVersion(), '3.3.0', '<')) {
                /**
                 * Add integration columns into plumrocket_newsletterpopup_popups
                 */
                $popupsTableName = $setup->getTable('plumrocket_newsletterpopup_popups');

                if ($connection->isTableExists($popupsTableName)) {
                    $connection->addColumn(
                        $popupsTableName,
                        'integration_enable',
                        [
                            'type' => Table::TYPE_TEXT,
                            'length' => 255,
                            'nullable' => true,
                            'comment' => 'Integration status JSON data',
                        ]
                    );

                    $connection->addColumn(
                        $popupsTableName,
                        'integration_mode',
                        [
                            'type' => Table::TYPE_TEXT,
                            'length' => 255,
                            'nullable' => true,
                            'comment' => 'Integration mode JSON data',
                        ]
                    );
                }

                /**
                 * Add integration_id column into lists table
                 */
                $listsTableName = $setup->getTable('plumrocket_newsletterpopup_mailchimp_list');

                if ($connection->isTableExists($listsTableName)) {
                    $connection->addColumn(
                        $listsTableName,
                        'integration_id',
                        [
                            'type' => Table::TYPE_TEXT,
                            'length' => 32,
                            'nullable' => true,
                            'after' => 'popup_id',
                            'comment' => 'Integration identifier',
                        ]
                    );
                }

                /**
                 * Add integration lists column for plumrocket_newsletterpopup_hold
                 */
                $holdTableName = $setup->getTable('plumrocket_newsletterpopup_hold');

                if ($connection->isTableExists($holdTableName)) {
                    $connection->addColumn(
                        $holdTableName,
                        'integration_lists',
                        [
                            'type' => Table::TYPE_TEXT,
                            'nullable' => true,
                            'comment' => 'Integration lists JSON data',
                        ]
                    );
                }
            }

            /**
             * Version 3.3.2
             */
            if (version_compare($context->getVersion(), '3.3.2', '<=')) {
                $setup->getConnection()->changeColumn(
                    $setup->getTable('plumrocket_newsletterpopup_mailchimp_list'),
                    'name',
                    'name',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 64
                    ]
                );
            }
        } catch (\Exception $e) {
        }

        $setup->endSetup();
    }
}
