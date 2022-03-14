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

namespace Plumrocket\AmpEmail\Api;

interface ComponentProductLocatorInterface extends \Plumrocket\AmpEmailApi\Model\LocatorInterface
{
    /**
     * @return ComponentProductLocatorInterface
     */
    public function resetData() : \Plumrocket\AmpEmailApi\Model\LocatorInterface;

    /**
     * @param array $productIds
     * @return \Plumrocket\AmpEmail\Api\ComponentProductLocatorInterface
     */
    public function setProductIds(array $productIds) : ComponentProductLocatorInterface;

    /**
     * @return int[]
     */
    public function getProductIds() : array;

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $products
     * @return \Plumrocket\AmpEmail\Api\ComponentProductLocatorInterface
     */
    public function setProducts(array $products) : ComponentProductLocatorInterface;

    /**
     * In manual testing method can return products from order
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProducts() : array;
}
