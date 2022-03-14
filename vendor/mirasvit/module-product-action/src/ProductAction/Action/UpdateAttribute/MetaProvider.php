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
 * @package   mirasvit/module-product-action
 * @version   1.0.9
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\ProductAction\Action\UpdateAttribute;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Ui\Component\Form\Element\Textarea;
use Mirasvit\ProductAction\Api\MetaProviderInterface;
use Mirasvit\ProductAction\Ui\Element;

class MetaProvider implements MetaProviderInterface
{
    const PARAM_ATTRIBUTE_KEYS   = 'mass_update_attributes';
    const PARAM_ATTRIBUTE_VALUES = 'mass_update_attributes_values';

    const IGNORED_TYPES
        = [
            'gallery',
            'image',
            'media_image',
        ];

    use Element\CheckboxSetTrait;
    use Element\CheckboxTrait;
    use Element\DateTrait;
    use Element\GroupTrait;
    use Element\EnqueueTrait;
    use Element\SelectTrait;
    use Element\TextTrait;

    private $attributeCollectionFactory;

    private $eavConfig;

    public function __construct(
        AttributeCollectionFactory $attributeCollectionFactory,
        EavConfig $eavConfig
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->eavConfig                  = $eavConfig;
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public function getMeta(): array
    {
        $attributes = [];

        foreach ($this->getAllAttributes() as $option) {
            if (!in_array($option['type'], self::IGNORED_TYPES)) {
                $attributes[] = $option;
            }
        }

        $meta = [
            $this->getCheckboxSet(
                '',
                self::PARAM_ATTRIBUTE_KEYS,
                $attributes,
                [
                    'component' => 'Mirasvit_ProductAction/js/elements/checkboxset',
                ]
            ),
            $this->getEnqueue(),
        ];

        $children = [];

        foreach ($attributes as $option) {
            $groupName = 'attribute_' . $option['value'];

            switch ($option['type']) {
                case 'select':
                    $children[$groupName] = $this->getSelect(
                        $groupName,
                        $option['label'],
                        $this->getAttributeOptions($option['value']),
                        [
                            'name'     => $groupName,
                            'visible'  => false,
                            'multiple' => false,
                        ]
                    );
                    break;
                case 'multiselect':
                    $children[$groupName] = $this->getSelect(
                        $groupName,
                        $option['label'],
                        $this->getAttributeOptions($option['value']),
                        [
                            'name'     => $groupName,
                            'visible'  => false,
                            'multiple' => true,
                        ]
                    );
                    break;
                case 'boolean':
                    $children[$groupName] = $this->getCheckbox(
                        $option['label'],
                        $groupName,
                        [
                            'name'     => $groupName,
                            'visible'  => false,
                            'multiple' => true,
                        ]
                    );
                    break;
                case 'date':
                    $children[$groupName] = $this->getDateField(
                        $option['label'],
                        $groupName,
                        [
                            'name'     => $groupName,
                            'visible'  => false,
                            'multiple' => true,
                        ]
                    );
                    break;
                case 'textarea':
                    $children[$groupName] = $this->elementText(
                        $groupName,
                        $option['label'],
                        [
                            'name'        => $groupName,
                            'visible'     => false,
                            'formElement' => Textarea::NAME,
                        ]
                    );
                    break;
                case 'text':
                default:
                    $children[$groupName] = $this->elementText(
                        $groupName,
                        $option['label'],
                        [
                            'name'    => $groupName,
                            'visible' => false,
                        ]
                    );
                    break;
            }
        }

        $meta[] = $this->elementGroup(
            $children,
            [
                'additionalClasses' => 'mst-product-action-attribute-values',
                'name'              => self::PARAM_ATTRIBUTE_VALUES,
            ]
        );

        return $meta;
    }

    private function getAllAttributes(): array
    {
        $collection = $this->attributeCollectionFactory->create();

        $options = [];
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        foreach ($collection as $attribute) {
            if (!$attribute->getFrontendLabel()) {
                continue;
            }

            $options[] = [
                'label' => $attribute->getFrontendLabel(),
                'value' => $attribute->getAttributeCode(),
                'id'    => $attribute->getId(),
                'type'  => $attribute->getFrontendInput(),
            ];
        }

        usort($options, function (array $a, array $b) {
            if ($a['label'] == $b['label']) {
                return 0;
            }

            return ($a['label'] < $b['label']) ? -1 : 1;
        });

        return $options;
    }

    private function getAttributeOptions(string $attributeCode): array
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);

        $source = $attribute->getSource();

        $options = $source->getAllOptions();

        // Options with 0 value do not select because 0 is equal to false. That is why we convert to string.
        foreach ($options as $k => $option) {
            if (!is_array($option['value'])) {
                $options[$k]['value'] = (string)$option['value'];
            }
        }

        return $options;
    }
}
