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



namespace Mirasvit\ProductKit\Plugin;

use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote\Item;
use Mirasvit\Core\Service\SerializeService;

/**
 * Adds kit item as separate cart item
 * @see \Magento\Quote\Model\Quote\Item::representProduct()
 */
class AddQuoteItemPlugin
{
    /**
     * @param Item     $item
     * @param callable $proceed
     * @param Product  $product
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRepresentProduct(Item $item, $proceed, $product)
    {
        $result = $proceed($product);

        /** @var \Magento\Catalog\Model\Product\Configuration\Item\Option $options */
        $options = $product->getCustomOptions();
        if (empty($options['info_buyRequest']) && empty($options['kit_info'])) {
            return $result;
        }
        if (!empty($options['kit_info'])) { // for grouped products
            $requestOptions = $options['kit_info']->getValue();
        } else {
            $requestOptions = $options['info_buyRequest']->getValue();
        }
        try {
            $requestOptions = SerializeService::decode($requestOptions);
        } catch (\Exception $e) {
            return $result;
        }

        if (isset($requestOptions['kit_id']) && isset($requestOptions['kit_info'])) {
            return false;
        }

        return $result;
    }
}
