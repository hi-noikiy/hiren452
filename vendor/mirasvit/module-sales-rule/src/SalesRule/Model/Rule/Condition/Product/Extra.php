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



namespace Mirasvit\SalesRule\Model\Rule\Condition\Product;

use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

class Extra extends AbstractCondition
{
    /**
     * Extra constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        $data['form_name'] = 'sales_rule_form';


        parent::__construct($context, $data);
    }

    /**
     * @return $this|AbstractCondition
     */
    public function loadAttributeOptions()
    {
        $result = [
            'is_special_price' => __('Has Special Price'),
            'is_discounted'    => __('Is Discounted'),
        ];

        asort($result);

        $this->setData('attribute_option', $result);

        return $this;
    }

    /**
     * @return $this|AbstractCondition
     */
    public function loadOperatorOptions()
    {
        $this->setData('operator_option', [
            '==' => __('is'),
            '!=' => __('is not'),
        ]);

        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'select';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        return [
            [
                'label' => __('Yes'),
                'value' => 1,
            ],
            [
                'label' => __('No'),
                'value' => 0,
            ],
        ];
    }

    /**
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        /** @var \Magento\Quote\Model\Quote\Item $model */

        $attr = $this->getData('attribute');

        switch ($attr) {
            case 'is_special_price':
                $product = $model->getProduct();
                $value   = $product->getPrice() == $product->getFinalPrice();

                return parent::validateAttribute($value ? 0 : 1);

            case 'is_discounted':
                $value = $model->getDiscountAmount();

                return parent::validateAttribute($value > 0 ? 1 : 0);
        }

        return true;
    }
}