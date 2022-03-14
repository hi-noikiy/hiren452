<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Api\Data;

interface KitInterface
{
    const TABLE_NAME = 'mst_product_kit_kit';

    const ID                    = 'kit_id';
    const NAME                  = 'name';
    const LABEL                 = 'label';
    const IS_ACTIVE             = 'is_active';
    const IS_SMART              = 'is_smart';
    const STORE_IDS             = 'store_ids';
    const CUSTOMER_GROUP_IDS    = 'customer_group_ids';
    const PRIORITY              = 'priority';
    const ACTIVE_FROM           = 'active_from';
    const ACTIVE_TO             = 'active_to';
    const STOP_RULES_PROCESSING = 'stop_rules_processing';
    const BLOCK_TITLE           = 'block_title';
    const PRICE_PATTERN         = 'price_pattern';

    const SMART_BLOCKS_DEFAULT = 5;
    const SMART_BLOCKS_AMOUNT  = 'smart_blocks_amount';

    const CREATED_AT = 'created_at';

//    const IS_SMART_ENABLED = 'is_smart_enabled';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTitle($value);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLabel($value);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsActive($value);

    /**
     * @return bool
     */
    public function isSmart();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsSmart($value);

    /**
     * @return int[]
     */
    public function getStoreIds();

    /**
     * @param int[] $value
     *
     * @return $this
     */
    public function setStoreIds(array $value);

    /**
     * @return int[]
     */
    public function getCustomerGroupIds();

    /**
     * @param int[] $value
     *
     * @return $this
     */
    public function setCustomerGroupIds(array $value);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setPriority($value);

    /**
     * @return string
     */
    public function getPricePattern();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPricePattern($value);

    /**
     * @return bool
     */
    public function isStopRulesProcessing();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setStopRulesProcessing($value);

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setDataUsingMethod($key, $value);
}
