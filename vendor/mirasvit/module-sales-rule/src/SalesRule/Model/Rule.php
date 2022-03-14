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



namespace Mirasvit\SalesRule\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\SalesRule\Api\Data\RuleInterface;

class Rule extends AbstractModel implements RuleInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Rule::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($value)
    {
        return $this->setData(self::PARENT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponSuccessMessage()
    {
        return $this->getData(self::COUPON_SUCCESS_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponSuccessMessage($value)
    {
        return $this->setData(self::COUPON_SUCCESS_MESSAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponErrorMessage()
    {
        return $this->getData(self::COUPON_ERROR_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponErrorMessage($value)
    {
        return $this->setData(self::COUPON_ERROR_MESSAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSkipCondition()
    {
        return $this->getData(self::SKIP_CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setSkipCondition($value)
    {
        return $this->setData(self::SKIP_CONDITION, $value);
    }

}