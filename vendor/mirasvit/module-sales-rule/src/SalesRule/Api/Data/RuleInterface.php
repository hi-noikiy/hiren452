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
 * @package   mirasvit/module-sales-rule
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SalesRule\Api\Data;

interface RuleInterface
{
    const TABLE_NAME = 'mst_sales_rule_rule';

    const ID        = 'rule_id';
    const PARENT_ID = 'parent_id';

    const COUPON_SUCCESS_MESSAGE = 'coupon_success_message';
    const COUPON_ERROR_MESSAGE   = 'coupon_error_message';
    const SKIP_CONDITION         = 'skip_condition';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setParentId($value);

    /**
     * @return string
     */
    public function getCouponSuccessMessage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCouponSuccessMessage($value);

    /**
     * @return string
     */
    public function getCouponErrorMessage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCouponErrorMessage($value);

    /**
     * @return string
     */
    public function getSkipCondition();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSkipCondition($value);
}