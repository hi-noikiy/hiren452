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

class MostCheapestType extends MostExpensiveType implements RuleTypeInterface
{
    /**
     * @return string
     */
    public function getType()
    {
        return 'mst_most_cheapest';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Discount for Most Cheapest product';
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function calculate($rule, $item, $qty)
    {
        $cheapestItem = $this->getCheapestItem($item->getQuote(), $rule);

        if ($cheapestItem !== $item) {
            return $this->context->discountDataFactory->create();
        }

        return $this->getDiscountData($rule, $cheapestItem, $qty);
    }

    /**
     * @param \Magento\Quote\Model\Quote    $quote
     * @param \Magento\SalesRule\Model\Rule $rule
     *
     * @return \Magento\Quote\Model\Quote\Item|false
     */
    private function getCheapestItem($quote, $rule)
    {
        $items = $this->context->getMatchingItems($quote, $rule);

        $item = false;
        $min  = false;
        foreach ($items as $itm) {
            $price = $this->context->validator->getItemPrice($itm);
            if ($price && ($min === false || $min > $price)) {
                $min  = $price;
                $item = $itm;
            }
        }

        $quoteRuleIds = $quote->getAppliedRuleIds() ? explode(',', $quote->getAppliedRuleIds()) : [];
        /** @var string $ids */
        $ids = $item->getAppliedRuleIds();
        $itemRuleIds  = $ids ? explode(',', $ids) : [];

        if (count($itemRuleIds) && count($quoteRuleIds) > count($itemRuleIds)) {
            return false;
        }

        return $item;
    }
}