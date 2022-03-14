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

interface KitItemInterface
{
    const TABLE_NAME = 'mst_product_kit_kit_item';

    const ID          = 'item_id';
    const KIT_ID      = 'kit_id';
    const PRODUCT_ID  = 'product_id';
    const POSITION    = 'position';
    const IS_OPTIONAL = 'is_optional';
    const IS_PRIMARY  = 'is_primary';
    const QTY         = 'qty';
    const CONDITIONS  = 'conditions';
    const IS_REMOVED  = 'is_removed';

    const DISCOUNT_TYPE   = 'discount_type';
    const DISCOUNT_AMOUNT = 'discount_amount';


    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setId($value);

    /**
     * @return int
     */
    public function getKitId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setKitId($value);

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setProductId($value);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setPosition($value);

    /**
     * @return bool
     */
    public function isOptional();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsOptional($value);

    /**
     * @return bool
     */
    public function isPrimary();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsPrimary($value);

    /**
     * @return int
     */
    public function getQty();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setQty($value);

    /**
     * @return string
     */
    public function getDiscountType();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDiscountType($value);

    /**
     * @return float
     */
    public function getDiscountAmount();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setDiscountAmount($value);

    /**
     * @return string
     */
    public function getConditions();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setConditions($value);

    /**
     * @return \Mirasvit\ProductKit\Model\Rule\Rule
     */
    public function getRule();

    /**
     * @param string $key
     *
     * @return array
     */
    public function getData($key = null);

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setDataUsingMethod($key, $value);
}
