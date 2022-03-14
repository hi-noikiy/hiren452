<?php

namespace JB\DynamicAttribute\Plugin\Magento\Swatches\Block\Product\Renderer;

class Configurable
{
    public function afterGetJsonConfig(\Magento\Swatches\Block\Product\Renderer\Configurable $subject, $result) {
        $jsonResult = json_decode($result, true);
        foreach ($subject->getAllowProducts() as $simpleProduct) {
            $id = $simpleProduct->getId();
            foreach($simpleProduct->getAttributes() as $attribute) {
                if(($attribute->getIsVisible() && $attribute->getIsVisibleOnFront()) || in_array($attribute->getAttributeCode(), ['sku','description','name','metal','center_diamond_weight','diamonds_authenticy','diamond_color','diamond_clarity','total_no_of_diamonds','total_diamonds_weight','baking_type','rhodium_plated_yes','setting_type','center_gia_certified_diamond']) ) { // <= Here you can put any attribute you want to see dynamic
                    $code = $attribute->getAttributeCode();
                    $value = (string)$attribute->getFrontend()->getValue($simpleProduct);
                    $jsonResult['dynamic'][$code][$id] = [
                        'value' => $value
                    ];
                }
            }
        }
        $result = json_encode($jsonResult);
        return $result;
    }
}