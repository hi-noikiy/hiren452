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
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AmpEmail\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Plumrocket\AmpEmail\Model\ResourceModel\Security\VerifiedSender as ResourceVerifiedSender;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        //installing new fields into email_template table
        $columns = [
            'pramp_email_enable' => [
                'type' => Table::TYPE_SMALLINT,
                'default' => 0,
                'comment' => 'AMP Email Enable'
            ],
            'pramp_email_content' => [
                'type' => Table::TYPE_TEXT,
                'default' => '',
                'comment' => 'AMP Email Content'
            ],
            'pramp_email_styles' => [
                'type' => Table::TYPE_TEXT,
                'default' => '',
                'comment' => 'AMP Email Styles'
            ],
            'pramp_email_mode' => [
                'type' => Table::TYPE_TEXT,
                'size' => 50,
                'comment' => 'AMP Email Mode'
            ],
            'pramp_email_testing_method' => [
                'type' => Table::TYPE_TEXT,
                'size' => 50,
                'comment' => 'AMP Email Testing Method'
            ],
            'pramp_email_automatic_emails' => [
                'type' => Table::TYPE_TEXT,
                'comment' => 'AMP Email Testing Method'
            ],
            'pramp_email_manual_email' => [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'comment' => 'AMP Email Testing Customer Email'
            ],
            'pramp_email_manual_order' => [
                'type' => Table::TYPE_INTEGER,
                'size' => 255,
                'comment' => 'AMP Email Testing Customer Order'
            ],
            'pramp_email_manual_send' => [
                'type' => Table::TYPE_TEXT,
                'comment' => 'AMP Email Test Recipient'
            ],
        ];

        foreach ($columns as $id => $data) {
            $this->addColumn($installer, 'email_template', $id, $data);
        }
        //end

        /**
         * Creating plumrocket_amp_email_sender table
         */
        $ampEmailsTable = $installer->getConnection()
            ->newTable($installer->getTable(ResourceVerifiedSender::MAIN_TABLE_NAME))
            ->addColumn(
                ResourceVerifiedSender::MAIN_TABLE_ID_FIELD_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'primary' => true],
                'Sender Email'
            )
            ->setComment('Plumrocket AMP Email Verified Senders');

        $installer->getConnection()->createTable($ampEmailsTable);

        $installer->endSetup();
    }

    /**
     * @param $installer
     * @param $table
     * @param $field
     * @param $data
     */
    private function addColumn(SchemaSetupInterface $installer, $table, $field, $data)
    {
        $installer->getConnection()->addColumn(
            $installer->getTable($table),
            $field,
            $data
        );
    }
}
