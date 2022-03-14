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

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Mirasvit\SalesRule\Api\Data\RuleTypeInterface;

class BuyXGetAmountYType implements RuleTypeInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var PriceCurrencyInterface 
     */
    private $priceCurrency;

    /**
     * BuyXGetAmountYType constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->context       = $context;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'mst_buy_x_get_amount_y';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Buy product X Get $ discount for product Y';
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

        $discountAmount = $this->priceCurrency->convert($rule->getDiscountAmount(), $item->getQuote()->getStore());
        $discountQty    = $rule->getDiscountQty();

        $conditions = $rule->getConditions()->asArray();

        if ($discountAmount < 0.01 || !isset($conditions['conditions'])) {
            return $discountData;
        }

        if ($discountQty && $qty > $discountQty) {
            $qty = $discountQty;
        }

        $discountData->setAmount($qty * $discountAmount);

        $discountData->setBaseAmount($qty * $discountAmount);

        $discountData->setOriginalAmount($qty * $discountAmount);

        $discountData->setBaseOriginalAmount($qty * $discountAmount);

        return $discountData;
    }
}