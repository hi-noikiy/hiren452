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

class AdditionalInfor extends \Magezon\ProductPagePdf\Block\Product\Element
{
    /**
     * @var array
     */
    public $additionData;

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        if (!empty($this->getAdditionalData())) {
            return parent::isEnabled();
        }
        return false;
    }
    
    /**
     * @param array $excludeAttr
     * @return array
     */
    public function getAdditionalData($excludeAttr = [])
    {
        if ($this->additionData == null) {
            $data = [];
            $product = $this->getProduct();
            $attributes = $product->getAttributes();
            foreach ($attributes as $attribute) {
                if ($this->isVisibleOnFrontend($attribute, $excludeAttr)) {
                    $value = $attribute->getFrontend()->getValue($product);

                    if ($value instanceof Phrase) {
                        $value = (string)$value;
                    } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                        $value = $this->priceCurrency->convertAndFormat($value);
                    }

                    if (is_string($value) && strlen(trim($value))) {
                        $data[$attribute->getAttributeCode()] = [
                            'label' => $attribute->getStoreLabel(),
                            'value' => $value,
                            'code' => $attribute->getAttributeCode(),
                        ];
                    }
                }
            }
            $this->additionData = $data;
        }
        return $this->additionData;
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @param array $excludeAttr
     * @return bool
     * @since 103.0.0
     */
    protected function isVisibleOnFrontend(
        \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute,
        array $excludeAttr
    ) {
        return ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr));
    }
}
