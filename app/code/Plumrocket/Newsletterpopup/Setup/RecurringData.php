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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Newsletterpopup\Setup;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface;
use Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterfaceFactory;
use Plumrocket\Newsletterpopup\Api\PopupFieldsRegistryInterface;
use Plumrocket\Newsletterpopup\Model\ResourceModel\FormField;
use Psr\Log\LoggerInterface;

/**
 * @since v3.10.0
 */
class RecurringData implements InstallDataInterface
{
    /**
     * @var \Plumrocket\Newsletterpopup\Api\PopupFieldsRegistryInterface
     */
    private $popupFieldsRegistry;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\ResourceModel\FormField
     */
    private $formFieldResource;

    /**
     * @var \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterfaceFactory
     */
    private $formFieldFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * RecurringData constructor.
     *
     * @param \Plumrocket\Newsletterpopup\Api\PopupFieldsRegistryInterface        $popupFieldsRegistry
     * @param \Plumrocket\Newsletterpopup\Model\ResourceModel\FormField           $formFieldResource
     * @param \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterfaceFactory $formFieldFactory
     * @param \Psr\Log\LoggerInterface                                            $logger
     */
    public function __construct(
        PopupFieldsRegistryInterface $popupFieldsRegistry,
        FormField $formFieldResource,
        PopupFieldDataInterfaceFactory $formFieldFactory,
        LoggerInterface $logger
    ) {
        $this->popupFieldsRegistry = $popupFieldsRegistry;
        $this->formFieldResource = $formFieldResource;
        $this->formFieldFactory = $formFieldFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installedFields = $this->formFieldResource->getAllFieldsNames();

        $fields = $this->popupFieldsRegistry->getList();

        if ($fields) {
            try {
                foreach ($fields as $fieldName => $fieldData) {
                    if (! in_array($fieldName, $installedFields, true)) {
                        /** @var \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface $field */
                        $field = $this->formFieldFactory->create();

                        $field->setName($fieldName)
                              ->setLabel($fieldData[PopupFieldDataInterface::LABEL])
                              ->setSortOrder((int) $fieldData[PopupFieldDataInterface::SORT_ORDER])
                              ->setEnabled((int)($fieldData[PopupFieldDataInterface::ENABLED] ?? 0))
                              ->setPopupId((int)($fieldData[PopupFieldDataInterface::POPUP_ID] ?? 0));

                        $this->formFieldResource->save($field);
                    }
                }
            } catch (AlreadyExistsException $e) {
                $this->logger->error($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
