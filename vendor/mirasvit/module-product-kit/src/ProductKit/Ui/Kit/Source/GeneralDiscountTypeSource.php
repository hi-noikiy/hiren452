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

use Mirasvit\ProductKit\Model\ConfigProvider;

class GeneralDiscountTypeSource extends DiscountTypeSource
{
    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        $options[] = [
            'label' => __('Complex'),
            'value' => ConfigProvider::DISCOUNT_TYPE_COMPLEX,
        ];

        return $options;
    }
}
