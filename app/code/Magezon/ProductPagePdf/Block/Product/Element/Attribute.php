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

namespace Magezon\ProductPagePdf\Block\Product\Element;

class Attribute extends \Magezon\ProductPagePdf\Block\Product\Element
{
    /**
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this->getAttributeOptions('size') || $this->getAttributeOptions('color')) {
            return parent::isEnabled();
        }
        return false;
    }
    
    /**
     * @param string $attibuteCode
     * @return array
     */
    public function getAttributeOptions($attibuteCode)
    {
        $product = $this->getProduct();
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
            $options = [];
            foreach ($attributes as $attribute) {
                $eavAttribute = $attribute->getProductAttribute();
                if (0 == strcmp($attibuteCode, $eavAttribute->getAttributeCode())) {  
                    $options = $attribute->getOptions() ?: [];  
                }  
            }
            return $options;
        }
    }
}
