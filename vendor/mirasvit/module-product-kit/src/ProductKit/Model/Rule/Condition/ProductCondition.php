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
use Magento\CatalogRule\Model\Rule\Condition\Product as RuleConditionProduct;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Rule\Block\Editable as EditableBlock;
use Mirasvit\ProductKit\Service\AreaContextService;

/**
 * @method string getKind()
 * @method $this setKind($value)
 * @method string getPrefix()
 * @method string getId()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductCondition extends RuleConditionProduct
{
    private $areaContextService;

    private $productType;

    private $queryBuilder;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ProductType $productType,
        AreaContextService $areaContextService,
        QueryBuilder $queryBuilder,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat
    ) {
        $this->areaContextService = $areaContextService;
        $this->productType        = $productType;
        $this->queryBuilder       = $queryBuilder;

        parent::__construct($context, $backendData, $config, $productFactory, $productRepository, $productResource, $attrSetCollection, $localeFormat);
    }

    public function getDefaultOperatorInputByType()
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

    public function getValueSelectOptions()
    {
        $options = parent::getValueSelectOptions();
        if (is_array($options)) {
            $options = array_merge([
                [
                    'value' => '<==>',
                    'label' => __('Current Product "%1"', $this->getAttributeName()),
                ],
            ], $options);
        }

        return $options;
    }

    public function getOperatorElementHtml()
    {

        $elementId   = sprintf('%s__%s__kind', $this->getPrefix(), $this->getId());
        $elementName = sprintf($this->elementName . '[%s][%s][kind]', $this->getPrefix(), $this->getId());

        $options   = [
            [
                'value' => 'value',
                'label' => __('Exact value'),
            ],
            [
                'value' => 'product',
                'label' => __('Current Product "%1"', $this->getAttributeName()),
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

    public function getSqlCondition(Collection $collection)
    {
        $select = $collection->getSelect();

        $field = $this->queryBuilder->joinAttribute($select, $this->getAttributeObject());

        if ($this->getKind() == 'product') {
            $values = $this->areaContextService->getAttributeValue($this->getAttribute());

            if ($values) {
                if (empty($values[0])) {
                    $values[0] = -1;
                }

                $condition = $this->queryBuilder->buildCondition($field, $this->getOperator(), explode(',', $values[0]));
            } else {
                $op = $this->getOperator() == '()' ? '<==>' : '<=>';

                $condition = $this->queryBuilder->buildCondition($field, $op, $this->getValueParsed());
            }
        } else {
            $condition = $this->queryBuilder->buildCondition($field, $this->getOperator(), $this->getValueParsed());
        }

        return $condition;
    }

    public function loadArray($arr)
    {
        $result = parent::loadArray($arr);
        $this->setKind(isset($arr['kind']) ? $arr['kind'] : false);

        return $result;
    }

    public function asArray(array $arrAttributes = [])
    {
        $result = parent::asArray($arrAttributes);

        $result['kind'] = $this->getKind();

        return $result;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setElementName($name)
    {
        $this->elementName = $name;

        return $this;
    }

    /**
     * @param array $attributes
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);

        $attributes = array_merge($attributes, [
            'type_id'          => __('Product Type'),
        ]);
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        if ($this->getAttribute() === 'type_id') {
            return 'select';
        }

        return parent::getInputType();
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        if ($this->getAttribute() === 'type_id') {
            return 'select';
        }

        return parent::getValueElementType();
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareValueOptions()
    {
        // Check that both keys exist. Maybe somehow only one was set not in this routine, but externally.
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        // Get array of select options. It will be used as source for hashed options
        if ($this->getAttribute() === 'type_id') {
            $selectOptions = null;
            $typeOptions = $this->productType->getOptionArray();
            $selectOptions = [];
            foreach ($typeOptions as $key => $option) {
                $selectOptions[] = ['label' => $option, 'value' => $key];
            }

            // Set new values only if we really got them
            if ($selectOptions !== null) {
                // Overwrite only not already existing values
                if (!$selectReady) {
                    $this->setData('value_select_options', $selectOptions);
                }
                if (!$hashedReady) {
                    $hashedOptions = [];
                    foreach ($selectOptions as $o) {
                        $hashedOptions[$o['value']] = $o['label'];
                    }
                    $this->setData('value_option', $hashedOptions);
                }
            }
        } else {
            parent::_prepareValueOptions();
        }

        return $this;
    }
}
