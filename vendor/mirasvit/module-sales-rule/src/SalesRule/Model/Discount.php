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

use Magento\SalesRule\Model\Rule\Action\Discount\DiscountInterface;
use Mirasvit\SalesRule\Api\Data\RuleTypeInterface;

class Discount implements DiscountInterface
{
    /**
     * @var RuleTypeInterface
     */
    private $ruleType;

    /**
     * Discount constructor.
     * @param RuleTypeInterface $ruleType
     */
    public function __construct(
        RuleTypeInterface $ruleType
    ) {
        $this->ruleType = $ruleType;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate($rule, $item, $qty)
    {
        return $this->ruleType->calculate($rule, $item, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function fixQuantity($qty, $rule)
    {
        return $qty;
    }
}