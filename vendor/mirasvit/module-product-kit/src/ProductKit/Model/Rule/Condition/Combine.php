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



namespace Mirasvit\ProductKit\Model\Rule\Condition;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Rule\Model\Condition\Context;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    private $productCondition;

    protected $elementName = 'conditions';

    /**
     * @param ProductCondition $productCondition
     * @param Context          $context
     * @param string           $elementName
     * @param array $data
     */
    public function __construct(
        ProductCondition $productCondition,
        Context $context,
        $elementName = '',
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productCondition = $productCondition;

        $this->setData('type', self::class);
        if ($elementName) {
            $this->elementName = $elementName;
        }
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setElementName($name)
    {
        $this->elementName = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => self::class,
                'label' => __('Conditions Combination'),
            ],
        ]);

        $pool = [
            'Product Attributes' => $this->productCondition,
        ];

        foreach ($pool as $label => $conditionModel) {
            $pageAttributes = $conditionModel->loadAttributeOptions()->getData('attribute_option');

            $attributes = [];
            foreach ($pageAttributes as $code => $label) {
                $attributes[] = [
                    'value' => get_class($conditionModel) . '|' . $code,
                    'label' => $label,
                ];
            }

            $conditions = array_merge_recursive($conditions, [
                [
                    'label' => __($label),
                    'value' => $attributes,
                ],
            ]);
        }

        return $conditions;
    }

    public function applyConditions(Collection $productCollection)
    {
        $sqlCondition = $this->getSqlCondition($productCollection);

        if ($sqlCondition) {
            $productCollection->getSelect()->where($sqlCondition);
        }

        return $this;
    }

    public function getSqlCondition(Collection $productCollection)
    {
        $sqlCondition = [];

        /** @var \Magento\Rule\Model\Condition\AbstractCondition $condition */
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
