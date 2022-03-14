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

class ButtonBorderStyle implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Theme defaults')], 
            ['value' => 'solid', 'label' => __('Solid')], 
            ['value' => 'dotted', 'label' => __('Dotted')],
            ['value' => 'dashed', 'label' => __('Dashed')],
            ['value' => 'none', 'label' => __('None')],
            ['value' => 'hidden', 'label' => __('Hidden')],
            ['value' => 'double', 'label' => __('Double')]
        ];
    }
}
