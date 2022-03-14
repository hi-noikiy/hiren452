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

namespace Plumrocket\Newsletterpopup\Model\Popup;

use Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface;

/**
 * @since v3.10.0
 */
class GetFields
{
    /**
     * @var \Plumrocket\Newsletterpopup\Model\ResourceModel\FormField\CollectionFactory
     */
    private $formFieldCollectionFactory;

    /**
     * Registry constructor.
     *
     * @param \Plumrocket\Newsletterpopup\Model\ResourceModel\FormField\CollectionFactory $formFieldCollectionFactory
     */
    public function __construct(
        \Plumrocket\Newsletterpopup\Model\ResourceModel\FormField\CollectionFactory $formFieldCollectionFactory
    ) {
        $this->formFieldCollectionFactory = $formFieldCollectionFactory;
    }

    /**
     * @param int  $popupId
     * @param bool $onlyEnabled
     * @return \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface[]
     */
    public function execute(int $popupId, bool $onlyEnabled = true): array
    {
        /** @var \Plumrocket\Newsletterpopup\Model\ResourceModel\FormField\Collection $formFieldCollection */
        $formFieldCollection = $this->formFieldCollectionFactory->create();

        $formFieldCollection->addFieldToFilter(PopupFieldDataInterface::POPUP_ID, $popupId);

        if ($onlyEnabled) {
            $formFieldCollection = $formFieldCollection->addFieldToFilter(
                PopupFieldDataInterface::ENABLED,
                1
            );
        }

        $formFieldCollection->getSelect()->order(
            [PopupFieldDataInterface::SORT_ORDER, PopupFieldDataInterface::LABEL]
        );

        return $formFieldCollection->getItems();
    }
}
