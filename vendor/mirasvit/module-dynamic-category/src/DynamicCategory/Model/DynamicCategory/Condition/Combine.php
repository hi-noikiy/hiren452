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
 * @package   mirasvit/module-dynamic-category
 * @version   1.0.17
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



declare(strict_types=1);

namespace Mirasvit\DynamicCategory\Model\DynamicCategory\Condition;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Rule\Model\Condition\Context;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    private $productCondition;

    private $smartConditions = [];

    private $sourceCondition;

    public function __construct(
        ProductCondition $productCondition,
        SmartCondition\IsNewCondition $isNewCondition,
        SmartCondition\IsSalableCondition $isSalableCondition,
        SmartCondition\OnSaleCondition $onSaleCondition,
        SmartCondition\RatingCondition $ratingCondition,
        SmartCondition\ReviewCondition $reviewCondition,
        Msi\SourceCondition $sourceCondition,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productCondition = $productCondition;
        $this->sourceCondition  = $sourceCondition;
        $this->smartConditions  = [
            $isNewCondition,
            $isSalableCondition,
            $onSaleCondition,
            $ratingCondition,
            $reviewCondition,
        ];

        $this->setData('type', self::class);
    }

    public function getNewChildSelectOptions(): array
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => self::class,
                'label' => (string)__('Conditions Combination'),
            ],
        ]);

        $pool = [
            'Product Attributes' => $this->productCondition,
        ];

        foreach ($pool as $label => $conditionModel) {
            $conditionAttributes = $conditionModel->loadAttributeOptions()->getData('attribute_option');

            $attributes = [];
            foreach ($conditionAttributes as $code => $attributeLabel) {
                $attributes[] = [
                    'value' => get_class($conditionModel) . '|' . $code,
                    'label' => (string)$attributeLabel,
                ];
            }

            $conditions = array_merge_recursive($conditions, [
                [
                    'label' => (string)__($label),
                    'value' => $attributes,
                ],
            ]);
        }


        $smartValues = [];
        foreach ($this->smartConditions as $condition) {
            $smartValues[] = [
                'label' => $condition->getAttributeElementHtml(),
                'value' => get_class($condition),
            ];
        }

        $conditions[] = [
            'label' => (string)__('Smart Attributes'),
            'value' => $smartValues,
        ];


        $conditions[] = [
            'label' => (string)__('MSI'),
            'value' => [
                [
                    'label' => $this->sourceCondition->getAttributeElementHtml(),
                    'value' => get_class($this->sourceCondition),
                ],
            ],
        ];

        return $conditions;
    }

    public function collectValidatedAttributes(Collection $collection): Combine
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($collection);
        }

        return $this;
    }

    public function applyConditions(Collection $productCollection): Combine
    {
        $sqlCondition = $this->getSqlCondition($productCollection);

        if ($sqlCondition) {
            $productCollection->getSelect()->where($sqlCondition);
        }

        return $this;
    }

    public function getSqlCondition(Collection $productCollection): string
    {
        $sqlCondition = [];

        foreach ($this->getConditions() as $condition) {
            $sql = $condition->getSqlCondition($productCollection);

            if ($sql) {
                $sqlCondition[] = "({$sql})";
            }
        }

        if (!count($sqlCondition)) {
            return '';
        }

        $sql = $this->getData('aggregator') === 'all'
            ? implode(' AND ', $sqlCondition)
            : implode(' OR ', $sqlCondition);

        return "({$sql})";
    }
}
