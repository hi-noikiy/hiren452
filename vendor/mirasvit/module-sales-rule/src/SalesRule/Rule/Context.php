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

use Magento\SalesRule\Model\Rule\Action\Discount\DataFactory as DiscountDataFactory;
use Magento\SalesRule\Model\Validator;

class Context
{
    /**
     * @var DiscountDataFactory
     */
    public $discountDataFactory;

    /**
     * @var Validator
     */
    public $validator;

    /**
     * Context constructor.
     * @param DiscountDataFactory $discountDataFactory
     * @param Validator $validator
     */
    public function __construct(
        DiscountDataFactory $discountDataFactory,
        Validator $validator
    ) {
        $this->discountDataFactory = $discountDataFactory;
        $this->validator           = $validator;
    }

    /**
     * @param \Magento\Quote\Model\Quote    $quote
     * @param \Magento\SalesRule\Model\Rule $rule
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    public function getMatchingItems($quote, $rule)
    {
        $allItems = $quote->getAllItems();

        $items = [];

        foreach ($allItems as $item) {
            /** @var mixed $actions */
            $actions = $rule->getActions();
            if (!$actions->validate($item)) {
                continue;
            }

            $items[] = $item;
        }

        return $items;
    }
}