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

use Magento\Backend\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\CatalogRule\Model\Rule\Condition\Product as RuleConditionProduct;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Block\Editable as EditableBlock;
use Magento\Rule\Model\Condition\Context;

/**
 * @method string getPrefix()
 * @method string getId()
 */
class ProductCondition extends RuleConditionProduct
{
    private $queryBuilder;

    public function __construct(
        QueryBuilder $queryBuilder,
        Context $context,
        Data $backendData,
        Config $config,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        Product $productResource,
        Collection $attrSetCollection,
        FormatInterface $localeFormat
    ) {
        $this->queryBuilder = $queryBuilder;

        parent::__construct($context, $backendData, $config, $productFactory, $productRepository, $productResource, $attrSetCollection, $localeFormat);
    }

    public function getDefaultOperatorInputByType(): array
    {
        return [
            'string'      => ['==', '!=', '>=', '>', '<=', '<', '{}', '!{}', '()', '!()'],
            'numeric'     => ['==', '!=', '>=', '>', '<=', '<', '()', '!()'],
            'date'        => ['==', '>=', '<='],
            'select'      => ['==', '!=', '<=>'],
            'boolean'     => ['==', '!=', '<=>'],
            'multiselect' => ['()', '!()'],
            'category'    => ['()', '!()'],
            'grid'        => ['()', '!()'],
        ];
    }

    public function getOperatorElementHtml(): string
    {
        $elementId   = sprintf('%s__%s__kind', $this->getPrefix(), $this->getId());
        $elementName = sprintf($this->elementName . '[%s][%s][kind]', $this->getPrefix(), $this->getId());

        $options   = [
            [
                'value' => 'value',
                'label' => __('Exact value'),
            ],
        ];
        $valueName = $options[0]['label'];

        foreach ($options as $option) {
            if ($option['value'] == $this->getKind()) {
                $valueName = $option['label'];
            }
        }

        $element = $this->getForm()->addField(
            $elementId,
            'select',
            [
                'name'           => $elementName,
                'values'         => $options,
                'value'          => $this->getKind(),
                'value_name'     => $valueName,
                'data-form-part' => $this->getFormName(),
            ]
        );
        /** @var EditableBlock $editable */
        $editable = $this->_layout->getBlockSingleton(EditableBlock::class);
        $element->setRenderer($editable);

        $script = '
            <script>
                 require(["jquery"], (function($) {
                     var $el = $("#' . $elementId . '");
                     setInterval(function() {
                        update();
                     }, 10);

                     $el.on("change", function(e) {
                         update();
                     });

                     function update() {
                         var $val = $($(".rule-param", $el.closest("li"))[2]);

                         $el.val() === "product" ? $val.hide() : $val.show();
                     }
                 }));
            </script>
            ';

        return parent::getOperatorElementHtml() . $element->toHtml() . $script;
    }

    public function getSqlCondition(ProductCollection $collection): string
    {
        $value = is_array($this->getValue()) ? implode(',', $this->getValue()) : (string)$this->getValue();

        return $this->queryBuilder->buildCondition(
            $collection->getSelect(),
            $this->getAttribute(),
            $this->getOperator(),
            $value
        );
    }

    public function validate(AbstractModel $model): bool
    {
        $value = $model->getData($this->getAttribute());

        $value = $this->_prepareDatetimeValue($value, $model);
        $value = $this->_prepareMultiselectValue($value, $model);

        $model->setData($this->getAttribute(), $value);

        return parent::validate($model);
    }

    /**
     * @param ProductCollection $productCollection
     *
     * @return $this|RuleConditionProduct
     */
    public function collectValidatedAttributes($productCollection)
    {
        $attribute = $this->getAttribute();
        if ('quantity_and_stock_status' != $attribute) {
            if ($productCollection->getEntity()->getAttribute($attribute)) {
                return parent::collectValidatedAttributes($productCollection);
            }
        }

        return $this;
    }

    public function getJsFormObject(): string
    {
        return 'rule_dynamic_category_conditions_fieldset';
    }

    protected function _addSpecialAttributes(array &$attributes): void
    {
        parent::_addSpecialAttributes($attributes);

        $attributes['type_id'] = __('Type');
    }

    protected function _prepareValueOptions(): self
    {
        if ($this->getAttribute() === 'type_id') {
            return $this;
        }

        return parent::_prepareValueOptions();
    }
}
