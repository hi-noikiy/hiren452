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

namespace Mirasvit\ProductAction\Action\CopyAttributes;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Mirasvit\ProductAction\Action\UpdateAttribute\MetaProvider as AttributeMetaProvider;
use Mirasvit\ProductAction\Api\MetaProviderInterface;
use Mirasvit\ProductAction\Ui\Element;

class MetaProvider implements MetaProviderInterface
{
    const PARAM_COPY_FROM       = 'attributes_copy_from';
    const PARAM_COPY_ATTRIBUTES = 'copy_attributes';

    use Element\CheckboxSetTrait;
    use Element\CheckboxTrait;
    use Element\ProductSelectorTrait;
    use Element\EnqueueTrait;

    private $attributeCollectionFactory;

    public function __construct(
        AttributeCollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    public function getMeta(): array
    {

        $attributes = [];

        foreach ($this->getDefaultAttributes() as $option) {
            $attributes[] = $option;
        }

        foreach ($this->getUserDefinedAttributes() as $option) {
            if (!in_array($option['type'], AttributeMetaProvider::IGNORED_TYPES)) {
                $attributes[] = $option;
            }
        }

        $meta = [
            $this->elementProductSelector(self::PARAM_COPY_FROM, 'Copy from'),
            $this->getCheckboxSet('Select required attributes or leave empty to copy all', self::PARAM_COPY_ATTRIBUTES, $attributes, ['additionalClasses' => 'checkbox-set-container']),
            $this->getEnqueue(),
        ];


        return $meta;
    }

    private function getUserDefinedAttributes(): array
    {
        $collection = $this->attributeCollectionFactory->create()
            ->addFieldToFilter('is_user_defined', '1');

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

    public function getDefaultAttributes()
    {
        return [
            [
                'label'  => (string)__('Description'),
                'value'  => 'description',
                'id'     => 'description',
                'method' => 'Description',
            ],
            [
                'label'  => (string)__('Short Description'),
                'value'  => 'short_description',
                'id'     => 'short_description',
                'method' => 'ShortDescription',
            ],
            [
                'label'  => (string)__('Meta Description'),
                'value'  => 'meta_description',
                'id'     => 'meta_description',
                'method' => 'MetaDescription',
            ],
            [
                'label'  => (string)__('Meta Title'),
                'value'  => 'meta_title',
                'id'     => 'meta_title',
                'method' => 'MetaTitle',
            ],
            [
                'label'  => (string)__('Meta Keyword'),
                'value'  => 'meta_keyword',
                'id'     => 'meta_keyword',
                'method' => 'MetaKeyword',
            ],
            [
                'label'  => (string)__('Weight'),
                'value'  => 'weight',
                'id'     => 'weight',
                'method' => 'Weight',
            ],
            [
                'label'  => (string)__('Price'),
                'value'  => 'price',
                'id'     => 'price',
                'method' => 'Price',
            ],
            [
                'label'  => (string)__('Customer Group Price'),
                'value'  => 'tier_prices',
                'id'     => 'tier_prices',
                'method' => 'TierPrices',
            ],
        ];
    }
}
