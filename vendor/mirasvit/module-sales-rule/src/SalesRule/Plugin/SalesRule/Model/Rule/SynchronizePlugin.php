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



namespace Mirasvit\SalesRule\Plugin\SalesRule\Model\Rule;

use Mirasvit\SalesRule\Api\Data\RuleInterface;
use Mirasvit\SalesRule\Api\Repository\RuleRepositoryInterface;

class SynchronizePlugin
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * SynchronizePlugin constructor.
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        RuleRepositoryInterface $ruleRepository
    ) {
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterAfterSave($subject, $result)
    {
        /** @var \Magento\SalesRule\Model\Rule $subject */
        $rule = $this->ruleRepository->getByParentId($subject->getId());

        if (!$rule) {
            $rule = $this->ruleRepository->create();
        }

        $rule->setParentId($subject->getId())
            ->setCouponSuccessMessage($subject->getData(RuleInterface::COUPON_SUCCESS_MESSAGE))
            ->setCouponErrorMessage($subject->getData(RuleInterface::COUPON_ERROR_MESSAGE))
            ->setSkipCondition($subject->getData(RuleInterface::SKIP_CONDITION));

        $this->ruleRepository->save($rule);

        return $result;
    }

    /**
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterLoad($subject, $result)
    {
        /** @var \Magento\SalesRule\Model\Rule $subject */
        $rule = $this->ruleRepository->getByParentId($subject->getId());

        if ($rule) {
            $subject->setData(RuleInterface::COUPON_SUCCESS_MESSAGE, $rule->getCouponSuccessMessage())
                ->setData(RuleInterface::COUPON_ERROR_MESSAGE, $rule->getCouponErrorMessage())
                ->setData(RuleInterface::SKIP_CONDITION, $rule->getSkipCondition());
        }

        return $result;
    }
}