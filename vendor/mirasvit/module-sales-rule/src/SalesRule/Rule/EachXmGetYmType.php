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

class EachXmGetYmType implements RuleTypeInterface
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
     * EachXmGetYmType constructor.
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
        return 'mst_each_x_m_get_y_m';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'For each $X spend, give $Y discount';
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

        $discountStep   = (int)$rule->getDiscountStep();
        $discountAmount = (float)$rule->getDiscountAmount();
        $discountQty    = $rule->getDiscountQty();

        if ($discountStep <= 1) {
            return $discountData;
        }

        $items       = $this->context->getMatchingItems($item->getQuote(), $rule);
        $totalAmount = 0;

        foreach ($items as $itm) {
            $totalAmount += $this->context->validator->getItemBasePrice($itm) * $itm->getQty();
        }

        $totalAmount = min($totalAmount, $item->getQuote()->getBaseSubtotal());

        $multiplier = floor($totalAmount / $discountStep);

        if ($discountQty) {
            $multiplier = max($multiplier, $discountQty);
        }
        $totalDiscountAmount = $multiplier * $discountAmount;

        if ($totalDiscountAmount <= 0.01) {
            return $discountData;
        }

        $itemPrice             = $this->context->validator->getItemPrice($item);
        $baseItemPrice         = $this->context->validator->getItemBasePrice($item);
        $itemOriginalPrice     = $this->context->validator->getItemOriginalPrice($item);
        $baseItemOriginalPrice = $this->context->validator->getItemBaseOriginalPrice($item);

        $ruleTotals = 0;
        $quote      = $item->getQuote();
        $address    = $item->getAddress();
        $cartRules  = $address->getCartFixedRules();

        if (!isset($cartRules[$rule->getId()])) {
            $cartRules[$rule->getId()] = $totalDiscountAmount;
        }

        $availableDiscountAmount = (float)$cartRules[$rule->getId()];
        $discountType            = 'CartFixed' . $rule->getId();

        if ($availableDiscountAmount > 0) {
            $store       = $quote->getStore();
            $quoteAmount = $this->priceCurrency->convert($availableDiscountAmount, $store);

            $baseDiscountAmount = min($baseItemPrice * $qty, $availableDiscountAmount);
            $baseDiscountAmount = $this->priceCurrency->round($baseDiscountAmount);

            $availableDiscountAmount   -= $baseDiscountAmount;
            $cartRules[$rule->getId()] = $availableDiscountAmount;

            $discountData->setAmount($this->priceCurrency->round(min($itemPrice * $qty, $quoteAmount)));
            $discountData->setBaseAmount($baseDiscountAmount);
            $discountData->setOriginalAmount(min($itemOriginalPrice * $qty, $quoteAmount));
            $discountData->setBaseOriginalAmount($this->priceCurrency->round($baseItemOriginalPrice));
        }
        $address->setCartFixedRules($cartRules);

        return $discountData;
    }
}
