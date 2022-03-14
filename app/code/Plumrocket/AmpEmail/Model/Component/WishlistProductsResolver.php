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

class WishlistProductsResolver
{
    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    private $wishlistFactory;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * WishlistProductsResolver constructor.
     *
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     */
    public function __construct(\Magento\Wishlist\Model\WishlistFactory $wishlistFactory)
    {
        $this->wishlistFactory = $wishlistFactory;
    }

    /**
     * @param int  $customerId
     * @param bool $force
     * @return array
     */
    public function execute(int $customerId, bool $force = false) : array
    {
        if (0 === $customerId) {
            return [];
        }

        if (! array_key_exists($customerId, $this->cache) || $force) {
            /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
            $wishlist = $this->wishlistFactory->create();
            $wishlist->loadByCustomerId($customerId, true);

            $collection = $wishlist->getItemCollection();
            $collection->addFieldToSelect('product_id');

            $this->cache[$customerId] = $collection->getColumnValues('product_id');
        }

        return $this->cache[$customerId];
    }
}
