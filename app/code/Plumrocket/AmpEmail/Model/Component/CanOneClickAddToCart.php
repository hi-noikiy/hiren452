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
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
declare(strict_types=1);

namespace Plumrocket\AmpEmail\Model\Component;

class CanOneClickAddToCart implements \Plumrocket\AmpEmail\Api\CanOneClickAddToCartInterface
{
    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface|\Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function execute(\Magento\Catalog\Api\Data\ProductInterface $product) : bool
    {
        return $product->isSaleable()
            && $product->isInStock()
            && ! $product->getTypeInstance()->hasOptions($product)
            && \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE !== $product->getTypeId();
    }
}
