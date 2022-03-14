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
 * @package     Plumrocket_SizeChart
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\SizeChart\Api\Data;

interface SizechartInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const NAME = 'name';
    const BUTTON_LABEL = 'button_label';
    const CONTENT = 'content';
    const DISPLAY_TYPE = 'display_type';
    const STATUS = 'status';
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';
    const CONDITIONS_IS_MAIN = 'conditions_is_main';
    const CONDITIONS_PRIORITY = 'conditions_priority';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const STORE_ID = 'store_id';
    /**#@-*/

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return string|null
     */
    public function getButtonLabel();

    /**
     * @return string|null
     */
    public function getContent();

    /**
     * @return string|null
     */
    public function getDisplayType();

    /**
     * @return string|null
     */
    public function getStatus();

    /**
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @return string|null
     */
    public function getConditionsIsMain();

    /**
     * @return string|null
     */
    public function getConditionsPriority();

    /**
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * @return string|null
     */
    public function getStoreId();

    /**
     * @param $name
     * @return SizechartInterface
     */
    public function setName($name);

    /**
     * @param $buttonLabel
     * @return SizechartInterface
     */
    public function setButtonLabel($buttonLabel);

    /**
     * @param $content
     * @return SizechartInterface
     */
    public function setContent($content);

    /**
     * @param $displayType
     * @return SizechartInterface
     */
    public function setDisplayType($displayType);

    /**
     * @param $status
     * @return SizechartInterface
     */
    public function setStatus($status);

    /**
     * @param $updatedAt
     * @return SizechartInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @param $createdAt
     * @return SizechartInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @param $conditionsIsMain
     * @return SizechartInterface
     */
    public function setConditionsIsMain($conditionsIsMain);

    /**
     * @param $conditionsPriority
     * @return SizechartInterface
     */
    public function setConditionsPriority($conditionsPriority);

    /**
     * @param $conditionsSerialized
     * @return SizechartInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * @param $storeId
     * @return SizechartInterface
     */
    public function setStoreId($storeId);
}
