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

namespace Plumrocket\Newsletterpopup\Api\Data;

/**
 * @since v3.10.0
 */
interface PopupFieldDataInterface
{
    const POPUP_ID = 'popup_id';
    const NAME = 'name';
    const LABEL = 'label';
    const ENABLED = 'enable';
    const SORT_ORDER = 'sort_order';

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Retrieve linked Popup Id
     *
     * @return int
     */
    public function getPopupId(): int;

    /**
     * Retrieve system name of the field
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Retrieve text for frontend label of the field
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return int
     */
    public function getEnabled(): int;

    /**
     * Retrieve sort order in newsletter form
     *
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * Link Field to Popup
     *
     * @param int $popupIid
     * @return \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface
     */
    public function setPopupId($popupIid): PopupFieldDataInterface;

    /**
     * Set system name of the field
     *
     * @param string $name
     * @return \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface
     */
    public function setName($name): PopupFieldDataInterface;

    /**
     * Set text for frontend label of the field
     *
     * @param string $label
     * @return \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface
     */
    public function setLabel($label): PopupFieldDataInterface;

    /**
     * @param int $enabled
     * @return \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface
     */
    public function setEnabled($enabled): PopupFieldDataInterface;

    /**
     * @param int $sortOrder
     * @return \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface
     */
    public function setSortOrder($sortOrder): PopupFieldDataInterface;
}
