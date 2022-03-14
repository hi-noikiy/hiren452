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

class DisplayStyle implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = [
            'label' => __('Icon & Text'),
            'value' => '1'
        ];

        $options[] = [
            'label' => __('Icon'),
            'value' => '2'
        ];

        $options[] = [
            'label' => __('Text'),
            'value' => '3'
        ];

        return $options;
    }
}
