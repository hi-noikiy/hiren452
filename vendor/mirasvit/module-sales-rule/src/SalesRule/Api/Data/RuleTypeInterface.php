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

interface RuleTypeInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param \Magento\SalesRule\Model\Rule                $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float                                        $qty
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function calculate($rule, $item, $qty);
}