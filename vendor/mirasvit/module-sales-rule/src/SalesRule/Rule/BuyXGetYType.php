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



namespace Mirasvit\SalesRule\Rule;

use Mirasvit\SalesRule\Api\Data\RuleTypeInterface;

class BuyXGetYType implements RuleTypeInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * BuyXGetYType constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'mst_buy_x_get_y';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Buy product X Get % discount for product Y';
    }

    /**
     * @param \Magento\SalesRule\Model\Rule                $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float                                        $qty
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function calculate($rule, $item, $qty)
    {
        $discountData = $this->context->discountDataFactory->create();

        $discountAmount = min($rule->getDiscountAmount(), 100) / 100;
        $discountQty    = $rule->getDiscountQty();

        $conditions = $rule->getConditions()->asArray();

        if ($discountAmount < 0.01 || !isset($conditions['conditions'])) {
            return $discountData;
        }

        if ($discountQty && $qty > $discountQty) {
            $qty = $discountQty;
        }

        $itemPrice = $this->context->validator->getItemPrice($item);
        $discountData->setAmount($qty * $itemPrice * $discountAmount);

        $baseItemPrice = $this->context->validator->getItemBasePrice($item);
        $discountData->setBaseAmount($qty * $baseItemPrice * $discountAmount);

        $itemOriginalPrice = $this->context->validator->getItemOriginalPrice($item);
        $discountData->setOriginalAmount($qty * $itemOriginalPrice * $discountAmount);

        $baseItemOriginalPrice = $this->context->validator->getItemBaseOriginalPrice($item);
        $discountData->setBaseOriginalAmount($qty * $baseItemOriginalPrice * $discountAmount);

        return $discountData;
    }
}