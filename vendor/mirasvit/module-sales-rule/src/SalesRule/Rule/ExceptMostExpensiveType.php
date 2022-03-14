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

class ExceptMostExpensiveType implements RuleTypeInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * ExceptMostExpensiveType constructor.
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
        return 'mst_except_expensive';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Discount for All except Most Expensive product';
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function calculate($rule, $item, $qty)
    {
        $notExpensiveItems = $this->getNotExpensiveItems($item->getQuote(), $rule);

        if (!in_array($item, $notExpensiveItems)) {
            return $this->context->discountDataFactory->create();
        }

        // @todo: calculate for each product
        return $this->getDiscountData($rule, $item, $qty);
    }

    /**
     * @param \Magento\SalesRule\Model\Rule        $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem|false $item
     * @param int                                   $qty
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    protected function getDiscountData($rule, $item, $qty)
    {
        $discountData = $this->context->discountDataFactory->create();

        if (!$item) {
            return $discountData;
        }

        $discountAmount = min($rule->getDiscountAmount(), 100) / 100;
        $discountQty    = $rule->getDiscountQty();

        if ($discountAmount < 0.01) {
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

    /**
     * @param \Magento\Quote\Model\Quote    $quote
     * @param \Magento\SalesRule\Model\Rule $rule
     *
     * @return \Magento\Quote\Model\Quote\Item[]|false
     */
    private function getNotExpensiveItems($quote, $rule)
    {
        $items = $this->context->getMatchingItems($quote, $rule);

        $mostExpensive = false;
        $max           = false;
        foreach ($items as $itm) {
            $price = $this->context->validator->getItemPrice($itm);
            if ($max === false || $price > $max) {
                $max           = $price;
                $mostExpensive = $itm;
            }
        }

        $items = array_filter($items, function ($item) use ($mostExpensive) {
            return $item->getId() !== $mostExpensive->getId();
        });

        return $items;
    }
}