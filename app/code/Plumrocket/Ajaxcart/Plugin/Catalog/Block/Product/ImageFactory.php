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

namespace Plumrocket\Ajaxcart\Plugin\Catalog\Block\Product;

use Magento\Catalog\Block\Product\ImageFactory as CatalogImageFactory;

class ImageFactory
{
    /**
     * @param CatalogImageFactory $subject
     * @param callable $proceed
     * @param string $key
     * @param null $index
     * @return string
     */
    public function afterCreate(CatalogImageFactory $subject, $result)
    {
        if ($productId = $result->getProductId()) {
            $class = $result->getClass() . ' data-prac-' . $productId;
            $result->setClass(ltrim($class));
        }

        return $result;
    }
}
