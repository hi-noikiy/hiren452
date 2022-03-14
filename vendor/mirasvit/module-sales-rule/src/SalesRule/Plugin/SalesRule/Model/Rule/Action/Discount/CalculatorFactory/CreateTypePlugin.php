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



namespace Mirasvit\SalesRule\Plugin\SalesRule\Model\Rule\Action\Discount\CalculatorFactory;

use Mirasvit\SalesRule\Api\Repository\RuleTypeRepositoryInterface;
use Mirasvit\SalesRule\Model\DiscountFactory;

class CreateTypePlugin
{
    /**
     * @var RuleTypeRepositoryInterface
     */
    private $ruleTypeRepository;

    /**
     * @var DiscountFactory
     */
    private $discountFactory;

    /**
     * CreateTypePlugin constructor.
     * @param RuleTypeRepositoryInterface $ruleTypeRepository
     * @param DiscountFactory $discountFactory
     */
    public function __construct(
        RuleTypeRepositoryInterface $ruleTypeRepository,
        DiscountFactory $discountFactory
    ) {
        $this->ruleTypeRepository = $ruleTypeRepository;
        $this->discountFactory    = $discountFactory;
    }

    /**
     * @param mixed $subject
     * @param \Closure $proceed
     * @param string $type
     * @return \Mirasvit\SalesRule\Model\Discount|mixed
     */
    public function aroundCreate($subject, \Closure $proceed, $type)
    {
        foreach ($this->ruleTypeRepository->getList() as $ruleType) {
            if ($ruleType->getType() == $type) {
                $discount = $this->discountFactory->create([
                    'ruleType' => $ruleType,
                ]);

                return $discount;
            }
        }

        return $proceed($type);
    }
}