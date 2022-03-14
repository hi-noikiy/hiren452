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



namespace Mirasvit\ProductKit\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class BlockPositionSource implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'catalog_product_view',
                'label' => __('Product Page'),
            ],
            [
                'value' => 'checkout_cart_index',
                'label' => __('Shopping Cart Page'),
            ],
        ];
    }

    public function getOptions()
    {
        return [
            'catalog_product_view',
            'checkout_cart_index',
        ];
    }
}
