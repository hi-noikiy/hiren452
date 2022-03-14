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



namespace Mirasvit\ProductKit\Model\Total\Quote;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total as AddressTotal;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Mirasvit\ProductKit\Service\CartService;

class Discount extends AbstractTotal
{
    private $cartService;

    public function __construct(
        CartService $cartService
    ) {
        $this->cartService = $cartService;
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        AddressTotal $total
    ) {
        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $shippingAssignment->getShipping()->getAddress();

        $items = $address->getAllItems();

        if (!count($items)) {
            return $this;
        }

        $kit = $this->cartService->findSuitableProductKit($quote);

        if (!$kit) {
            return $this;
        }

        if ($total->getGrandTotal()) {
            $quoteTotal     = $total->getGrandTotal();
            $quoteBaseTotal = $total->getBaseGrandTotal();
        } else {
            $quoteTotal     = array_sum($total->getAllTotalAmounts());
            $quoteBaseTotal = array_sum($total->getAllBaseTotalAmounts());
        }

        $totalDiscount = 0;
        foreach ($items as $item) {
            $discountAmount = $this->cartService->getDiscountAmount($kit, $item);

            $item->setDiscountAmount($item->getDiscountAmount() + $discountAmount);
            $item->setBaseDiscountAmount($item->getBaseDiscountAmount() + $discountAmount);

            $totalDiscount += $discountAmount;
        }

        if ($total->getBaseGrandTotal()) {
            $total->setBaseGrandTotal($quoteBaseTotal - $totalDiscount);
        }
        if ($total->getGrandTotal()) {
            $total->setGrandTotal($quoteTotal - $totalDiscount);
        }

        $quote->save();

        return $this;
    }

    public function fetch(Quote $quote, AddressTotal $total)
    {
        $kit = $this->cartService->findSuitableProductKit($quote);
        if (!$kit) {
            return null;
        }

        $discount = 0;
        foreach ($quote->getAllItems() as $item) {
            $discount += $this->cartService->getDiscountAmount($kit, $item);
        }

        if (!$discount) {
            return null;
        }

        $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();


        $kitTotal = [
            'code'  => $this->getCode(),
            'title' => $kit->getLabel() ? __('Discount (%1)', $kit->getLabel()) : __('Discount'),
            'value' => $discount,
        ];

        $address->addTotal($kitTotal);

        return $kitTotal;
    }
}
