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



namespace Mirasvit\SalesRule\Plugin\SalesRule\Model\Rule\Metadata\ValueProvider;

use Magento\SalesRule\Model\Rule;
use Mirasvit\SalesRule\Api\Repository\RuleTypeRepositoryInterface;

class RegisterRulesPlugin
{
    /**
     * @var RuleTypeRepositoryInterface
     */
    private $ruleTypeRepository;

    /**
     * RegisterRulesPlugin constructor.
     * @param RuleTypeRepositoryInterface $ruleTypeRepository
     */
    public function __construct(
        RuleTypeRepositoryInterface $ruleTypeRepository
    ) {
        $this->ruleTypeRepository = $ruleTypeRepository;
    }

    /**
     * @param mixed $subject
     * @param \Closure $proceed
     * @param Rule $rule
     * @return mixed
     */
    public function aroundGetMetadataValues(
        $subject,
        \Closure $proceed,
        Rule $rule
    ) {
        $result = $proceed($rule);

        foreach ($this->ruleTypeRepository->getList() as $ruleType) {
            $result['actions']['children']['simple_action']['arguments']['data']['config']['options'][] = [
                'label' => $ruleType->getLabel(),
                'value' => $ruleType->getType(),
            ];
        }

        return $result;
    }
}