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



namespace Mirasvit\SalesRule\Plugin\SalesRule\Model\Rule\Condition\Product\Combine;

use Mirasvit\SalesRule\Model\Rule\Condition\Product\Extra as ExtraCondition;

class ExtendConditionsPlugin
{
    /**
     * @var ExtraCondition
     */
    private $extraCondition;

    /**
     * ExtendConditionsPlugin constructor.
     * @param ExtraCondition $extraCondition
     */
    public function __construct(
        ExtraCondition $extraCondition
    ) {
        $this->extraCondition = $extraCondition;
    }

    /**
     * @param mixed $subject
     * @param mixed $result
     * @return array
     */
    public function afterGetNewChildSelectOptions($subject, $result)
    {
        $conditions = [
            'Product Extra Attribute' => $this->extraCondition,
        ];

        foreach ($conditions as $title => $instance) {
            $option = [
                'label' => __($title),
                'value' => [],
            ];

            foreach ($instance->getAttributeOption() as $code => $label) {
                $option['value'][] = [
                    'label' => __($label),
                    'value' => get_class($instance) . '|' . $code,
                ];
            }

            $result[] = $option;
        }

        return $result;
    }
}