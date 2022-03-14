<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductPagePdf\Api\Data;

interface ProfileInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const PROFILE_ID            = 'profile_id';
    const NAME                  = 'name';
    const PROFILE               = 'profile';
    const IS_ACTIVE             = 'is_active';
    const FROM_DATE             = 'from_date';
    const TO_DATE               = 'to_date';
    const CREATION_TIME         = 'creation_time';
    const UPDATE_TIME           = 'update_time';
    const AUTO_DOWNLOAD         = 'auto_download';
    const BUTTON_TYPE           = 'button_type';
    const BUTTON_POSITION       = 'button_position';
    const PRIORITY              = 'priority';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const PRODUCT_TYPES         = 'product_types';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();
    
    /**
     * Get profile name
     *
     * @return string
     */
    public function getName();

    /**
     * Get profile
     *
     * @return string|null
     */
    public function getProfile();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Get from date
     *
     * @return string|null
     */
    public function getFromDate();

    /**
     * Get to date
     *
     * @return string|null
     */
    public function getToDate();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Get auto download
     *
     * @return bool|null
     */
    public function getAutoDownload();

    /**
     * Get button type
     *
     * @return int|null
     */
    public function getButtonType();

    /**
     * Get button position
     *
     * @return int|null
     */
    public function getButtonPosition();

    /**
     * Get priority
     *
     * @return int|null
     */
    public function getPriority();

    /**
     * Get conditions serialized
     *
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * Get product types
     *
     * @return string|null
     */
    public function getProductTypes();

    /**
     * Set ID
     *
     * @param int $id
     * @return ProfileInterface
     */
    public function setId($id);

    /**
     * Set profile name
     *
     * @param string $name
     * @return ProfileInterface
     */
    public function setName($name);

    /**
     * Set profile
     *
     * @param string $profile
     * @return ProfileInterface
     */
    public function setProfile($profile);

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return ProfileInterface
     */
    public function setIsActive($isActive);

    /**
     * Set from date
     *
     * @param string $fromDate
     * @return ProfileInterface
     */
    public function setFromDate($fromDate);

    /**
     * Set to date
     *
     * @param string $toDate
     * @return ProfileInterface
     */
    public function setToDate($toDate);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return ProfileInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return ProfileInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set auto download
     *
     * @param int|bool $autoDownload
     * @return ProfileInterface
     */
    public function setAutoDownload($autoDownload);

    /**
     * Set button type
     *
     * @param int $buttonType
     * @return ProfileInterface
     */
    public function setButtonType($buttonType);

    /**
     * Set button position
     *
     * @param int $buttonPosition
     * @return ProfileInterface
     */
    public function setButtonPosition($buttonPosition);

    /**
     * Set priority
     *
     * @param int $priority
     * @return ProfileInterface
     */
    public function setPriority($priority);

    /**
     * Set conditions serialized
     *
     * @param string $conditionsSerialized
     * @return ProfileInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * Set product types
     *
     * @param string $productTypes
     * @return ProfileInterface
     */
    public function setProductTypes($productTypes);

}