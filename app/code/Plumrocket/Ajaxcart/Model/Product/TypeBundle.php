<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Ajaxcart v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Model\Product;

class TypeBundle extends \Magento\Bundle\Model\Product\Type
{
    /**
     * Return product sku
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getSku($product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $dataHelper = $objectManager->get(\Plumrocket\Ajaxcart\Helper\Data::class);
        if (! $dataHelper->isAjaxcartRequest()) {
            return parent::getSku($product);
        }

        $sku = $product->getData('sku');
        if ($product->getCustomOption('option_ids')) {
            $sku = $this->getOptionSku($product, $sku);
        }
        return $sku;
    }
}
