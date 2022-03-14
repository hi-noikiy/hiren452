<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductPagePdf\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;

class TypeOptions implements ArrayInterface
{
    /**
     * @var \Magento\Catalog\Api\Data\ProductTypeInterface
     */
    protected $productTypes;

    /**
     * @param ConfigInterface $productTypeConfig
     * @param \Magento\Catalog\Api\Data\ProductTypeInterfaceFactory $productTypeFactory
     */
    public function __construct(
        ConfigInterface $productTypeConfig,
        \Magento\Catalog\Api\Data\ProductTypeInterfaceFactory $productTypeFactory
    ) {
        $this->productTypeConfig = $productTypeConfig;
        $this->productTypeFactory = $productTypeFactory;
    }

    public function getProductTypes()
    {
        if ($this->productTypes === null) {
            $productTypes = [];
            foreach ($this->productTypeConfig->getAll() as $productTypeData) {
                /** @var \Magento\Catalog\Api\Data\ProductTypeInterface $productType */
                $productType = $this->productTypeFactory->create();
                $productType->setName($productTypeData['name'])
                    ->setLabel($productTypeData['label']);
                $productTypes[] = $productType;
            }
            $this->productTypes = $productTypes;
        }
        return $this->productTypes;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = [
            'label' => __('Select All'),
            'value' => 'all'
        ];

        foreach ($this->getProductTypes() as $item) {
            $options[] = [
                'label' => $item->getLabel(),
                'value' => $item->getName()
            ];
        }
        return $options;
    }
}
