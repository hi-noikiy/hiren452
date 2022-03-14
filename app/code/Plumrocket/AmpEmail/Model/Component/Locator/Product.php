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

namespace Plumrocket\AmpEmail\Model\Component\Locator;

class Product implements \Plumrocket\AmpEmail\Api\ComponentProductLocatorInterface
{
    /**
     * @var integer[]
     */
    private $productIds = [];

    /**
     * @var \Magento\Catalog\Api\Data\ProductInterface[]
     */
    private $products = [];

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Product constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(\Magento\Catalog\Api\ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @return \Plumrocket\AmpEmail\Api\ComponentProductLocatorInterface
     */
    public function resetData() : \Plumrocket\AmpEmailApi\Model\LocatorInterface
    {
        $this->productIds = [];
        $this->products = [];
        return $this;
    }

    /**
     * @param array $productIds
     * @return \Plumrocket\AmpEmail\Api\ComponentProductLocatorInterface
     */
    public function setProductIds(array $productIds) : \Plumrocket\AmpEmail\Api\ComponentProductLocatorInterface
    {
        $this->productIds = $productIds;
        return $this;
    }

    /**
     * @return int[]
     */
    public function getProductIds() : array
    {
        return $this->productIds;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $products
     * @return \Plumrocket\AmpEmail\Api\ComponentProductLocatorInterface
     */
    public function setProducts(array $products) : \Plumrocket\AmpEmail\Api\ComponentProductLocatorInterface
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProducts() : array
    {
        if ($this->products) {
            return $this->products;
        }

        if ($this->getProductIds()) {
            foreach ($this->getProductIds() as $index => $productId) {
                try {
                    $this->products[] = $this->productRepository->getById($productId);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) { //@codingStandardsIgnoreLine
                    // Do nothing
                }
            }
        }

        return $this->products;
    }
}
