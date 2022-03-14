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

use Magento\Framework\Data\OptionSourceInterface;

class ButtonType implements OptionSourceInterface
{
    const ICON_TEXT = 1;
    const ICON      = 2;
    const TEXT      = 3;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
    	$options = [
            [
            	'label' => __('Icon & Text'),
            	'value' => self::ICON_TEXT,
            ],
            [
            	'label' => __('Icon'),
            	'value' => self::ICON,
            ],
            [
            	'label' => __('Text'),
            	'value' => self::TEXT,
            ]
        ];
        return $options;
    }
}
