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

class ProductAttribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param \Magento\Framework\App\State                                             $appState                   
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory 
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->appState = $appState;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        if ($this->isAdminStore()) {
            if ($this->options === NULL) {
                $collection = $this->attributeCollectionFactory->create();
                $collection->addFieldToFilter('is_visible', 1);
                $collection->addFieldToFilter('attribute_code', ['neq' => 'ppd_profile_id']);
                foreach ($collection as $attribute) {
                    $options[] = [
                        'label' => $attribute->getDefaultFrontendLabel(),
                        'value' => $attribute->getAttributeCode()
                    ];
                }
                $this->options = $options;
            }
        } else {
            $this->options = [];
        }
        return $this->options;
    }

    /**
     * @return bool
     */
    public function isAdminStore()
    {
        return ($this->appState->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML);
    }
}
