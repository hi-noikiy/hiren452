<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Ui\Kit\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\ProductKit\Model\ConfigProvider;

class DiscountTypeSource implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Fixed - $'),
                'value' => ConfigProvider::DISCOUNT_TYPE_FIXED,
            ],
            [
                'label' => __('Percentage - %'),
                'value' => ConfigProvider::DISCOUNT_TYPE_PERCENTAGE,
            ],
            [
                'label' => __('Relative - %, %, %'),
                'value' => ConfigProvider::DISCOUNT_TYPE_PERCENTAGE_RELATIVE,
            ],
            [
                'label' => __('Relative Kit - %, %, %'),
                'value' => ConfigProvider::DISCOUNT_TYPE_PERCENTAGE_KIT,
            ],
        ];
    }
}
