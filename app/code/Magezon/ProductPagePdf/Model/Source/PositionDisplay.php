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

class PositionDisplay implements OptionSourceInterface
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
            'label' => __('Under Title'),
            'value' => 1
        ];

        $options[] = [
            'label' => __('Under Add to cart'),
            'value' => 2
        ];

        $options[] = [
            'label' => __('After Tab List'),
            'value' => 3
        ];

        $options[] = [
            'label' => __('Append Social'),
            'value' => 4
        ];

        return $options;
    }
}
