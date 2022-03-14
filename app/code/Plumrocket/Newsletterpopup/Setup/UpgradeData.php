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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Plumrocket\Newsletterpopup\Model\TemplateFactory
     */
    private $templateFactory;

    /**
     * @var \Plumrocket\Newsletterpopup\Helper\TemplateFactory
     */
    private $helperTemplateFactory;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\FormFieldFactory
     */
    private $formFieldFactory;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\MailchimpListFactory
     */
    private $mailchimpListFactory;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */
    private $configDataCollectionFactory;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * UpgradeData constructor.
     *
     * @param \Plumrocket\Newsletterpopup\Model\TemplateFactory $templateFactory
     * @param \Plumrocket\Newsletterpopup\Helper\TemplateFactory $helperTemplateFactory
     * @param \Plumrocket\Newsletterpopup\Model\FormFieldFactory $formFieldFactory
     * @param \Plumrocket\Newsletterpopup\Model\MailchimpListFactory $mailchimpListFactory
     * @param \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configDataCollectionFactory
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Plumrocket\Newsletterpopup\Model\TemplateFactory $templateFactory,
        \Plumrocket\Newsletterpopup\Helper\TemplateFactory $helperTemplateFactory,
        \Plumrocket\Newsletterpopup\Model\FormFieldFactory $formFieldFactory,
        \Plumrocket\Newsletterpopup\Model\MailchimpListFactory $mailchimpListFactory,
        \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configDataCollectionFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->templateFactory = $templateFactory;
        $this->helperTemplateFactory = $helperTemplateFactory;
        $this->formFieldFactory = $formFieldFactory;
        $this->mailchimpListFactory = $mailchimpListFactory;
        $this->configDataCollectionFactory = $configDataCollectionFactory;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '3.0.3', '<')) {
            // Update all templates (cleared from h-tags)
            $rows = $this->helperTemplateFactory->create()->getAllData();

            foreach ($rows as $row) {
                $this->templateFactory
                    ->create()
                    ->setData($row)
                    ->setCanSaveBaseTemplates(true)
                    ->save();
            }
        }

        /**
         * Version 3.1.0
         */
        if (version_compare($context->getVersion(), '3.1.0', '<')) {
            $tableName = $setup->getTable('plumrocket_newsletterpopup_templates');

            /**
             * Update Thank you message for all templates
             */
            if ($connection->isTableExists($tableName) == true) {
                $connection->update(
                    $tableName,
                    [
                        'default_values' => new \Zend_Db_Expr('REPLACE(`default_values`, \'Thank you for your subscription.\', \'Thank you for your subscription!\')'),
                    ]
                );
            }

            /**
             * Update Fireworks Template
             */
            $templateModel = $this->templateFactory->create()->load(12);

            if ($templateModel && $templateModel->getId()) {
                $defaultValues = $templateModel->getData('default_values');
                $defaultValues = $defaultValues ? $this->serializer->unserialize($defaultValues) : [];
                $defaultValues['text_success'] = '<p><strong style="font-size: 28px; line-height: 33px;">Enjoy 15% OFF Your Entire Purchase.</strong></p>'
                    . '<p style="padding-top: 15px;">Enter Coupon Code:&nbsp;<strong style="color: #ca0b0b; background: #faffad; padding: 5px 7px; border-radius: 3px; border: 1px dashed #d4da65;">{{coupon_code}}</strong><br/>At Checkout</p>'
                    . '<p style="padding-top: 24px; color: #d00000;">Hurry! This Offer Ends in 2 HOURS!</p>';

                $templateModel->setData('default_values', $this->serializer->serialize($defaultValues))
                    ->setData('can_save_base_templates', true)
                    ->save();
            }

        }

        /**
         * Version 3.1.1
         */
        if (version_compare($context->getVersion(), '3.1.1', '<')) {
            // Add agreement field
            $data = [
                'name' => 'agreement',
                'label' => 'I have read and agree to the <a href="#" target="_blank">Terms of Service</a>',
                'enable' => 0,
                'sort_order' => 210,
                'popup_id' => 0,
            ];

            $this->formFieldFactory->create()->setData($data)->save();
        }

        /**
         * Version 3.1.2
         */
        if (version_compare($context->getVersion(), '3.1.2', '<')) {
            // Add Google reCaptcha
            $data = [
                'name' => 'recaptcha',
                'label' => 'Google reCaptcha',
                'enable' => 0,
                'sort_order' => 220,
                'popup_id' => 0,
            ];
            $this->formFieldFactory->create()->setData($data)->save();

            /**
             * Update Sticky Footer Bar Template
             */
            $templateModel = $this->templateFactory->create()->load(13);
            $codeStyle = $this->helperTemplateFactory->create()->getCodeStyle(13);

            if ($templateModel && $templateModel->getId() && $codeStyle !== null) {
                $templateModel->setData('style', $codeStyle['style'])
                    ->setData('can_save_base_templates', true)
                    ->save();
            }

            /**
             * Update Golden Black Template
             */
            $templateModel = $this->templateFactory->create()->load(9);
            $codeStyle = $this->helperTemplateFactory->create()->getCodeStyle(9);

            if ($templateModel && $templateModel->getId() && $codeStyle !== null) {
                $templateModel->setData('style', $codeStyle['style'])
                    ->setData('can_save_base_templates', true)
                    ->save();
            }

            /**
             * Update Golden Diamond Template
             */
            $templateModel = $this->templateFactory->create()->load(11);
            $codeStyle = $this->helperTemplateFactory->create()->getCodeStyle(11);

            if ($templateModel && $templateModel->getId() && $codeStyle !== null) {
                $templateModel->setData('style', $codeStyle['style'])
                    ->setData('can_save_base_templates', true)
                    ->save();
            }
        }

        /**
         * Version 3.2.0
         */
        if (version_compare($context->getVersion(), '3.2.0', '<')) {
            /** @var \Magento\Config\Model\ResourceModel\Config\Data\Collection $configDataCollection */
            $configDataCollection = $this->configDataCollectionFactory->create();
            $configDataCollection->addPathFilter('prnewsletterpopup/mailchimp');

            foreach ($configDataCollection as $item) {
                $path = $item->getData('path');
                $newPath = str_replace('/mailchimp/', '/integration/mailchimp/', $path);
                $item->setData('path', $newPath);
            }

            $configDataCollection->save();
        }

        /**
         * Version 3.3.0
         */
        if (version_compare($context->getVersion(), '3.3.0', '<')) {
            /**
             * Update all records and set mailchimp value for integration_id column
             */
            $tableName = $setup->getTable('plumrocket_newsletterpopup_mailchimp_list');

            if ($connection->isTableExists($tableName)) {
                $connection->update(
                    $tableName,
                    [
                        'integration_id' => 'mailchimp',
                    ],
                    'integration_id IS NULL'
                );
            }

            /**
             * Update all templates and replace {{mailchimp_fields}} to {{contact_lists}}
             */
            $tableName = $setup->getTable('plumrocket_newsletterpopup_templates');

            if ($connection->isTableExists($tableName)) {
                $connection->update(
                    $tableName,
                    [
                        'code' => new \Zend_Db_Expr('REPLACE(`code`, \'{{mailchimp_fields}}\', \'{{contact_lists}}\')'),
                    ]
                );
            }

            // Update all templates (changes for new integration fields)
            $rows = $this->helperTemplateFactory->create()->getAllData();

            foreach ($rows as $row) {
                $this->templateFactory
                    ->create()
                    ->setData($row)
                    ->setCanSaveBaseTemplates(true)
                    ->save();
            }

            /**
             * Enable mailchimp integration if the lists exist
             */
            $mailchimpLists = $this->mailchimpListFactory->create()
                ->getCollection()
                ->addFieldToSelect('popup_id')
                ->addFieldToFilter('integration_id', 'mailchimp')
                ->addFieldToFilter('enable', 1);

            $mailchimpLists->getSelect()->group('popup_id');

            $popupIds = $mailchimpLists->getColumnValues('popup_id');

            if ($popupIds) {
                $connection->update(
                    $setup->getTable('plumrocket_newsletterpopup_popups'),
                    [
                        'integration_enable' => '{"mailchimp":"1"}'
                    ],
                    'integration_enable IS NULL AND entity_id IN (' . join(',', $popupIds) . ')'
                );
            }
        }

        $setup->endSetup();
    }
}
