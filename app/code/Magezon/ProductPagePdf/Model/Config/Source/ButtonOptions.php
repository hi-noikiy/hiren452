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

namespace Magezon\ProductPagePdf\Model\Config\Source;

class ButtonOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Select Type')], 
            ['value' => 1, 'label' => __('Default')],
            ['value' => 2, 'label' => __('Modern Button')], 
            ['value' => 3, 'label' => __('Flat Button')],
            ['value' => 4, 'label' => __('3D Button')],
            ['value' => 5, 'label' => __('Gradient Button')],
            ['value' => 6, 'label' => __('Outline Button')]
        ];
    }
}
