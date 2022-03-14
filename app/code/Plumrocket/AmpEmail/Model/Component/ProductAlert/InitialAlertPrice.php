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

namespace Plumrocket\AmpEmail\Model\Component\ProductAlert;

class InitialAlertPrice
{
    /**
     * @var array
     */
    private $data = ['products' => []];

    /**
     * @param array $result
     * @return array
     */
    public function render(array $result) : array
    {
        $result[$this->getCustomer()][$this->getWebsite()] = [
            'products' => $this->getProducts(),
            'store' => $this->getStoreId()
        ];
        return $result;
    }

    /**
     * @return $this
     */
    public function clean() : self
    {
        $this->data = ['products' => []];
        return $this;
    }

    /**
     * @param \Magento\Store\Model\Website $website
     * @return $this
     */
    public function setWebsite(\Magento\Store\Model\Website $website) : self
    {
        $this->data['website'] = (int) $website->getId();
        return $this;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId(int $storeId) : self
    {
        $this->data['store'] = $storeId;
        return $this;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param                                            $initialPrice
     */
    public function addInitialPrice(\Magento\Catalog\Api\Data\ProductInterface $product, $initialPrice)
    {
        $this->data['products'][] = [
            'id' => (int) $product->getId(),
            'price' => $initialPrice,
        ];
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return $this
     */
    public function setCustomerData(\Magento\Customer\Api\Data\CustomerInterface $customer) : self
    {
        $this->data['customer'] = (int) $customer->getId();
        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId() : int
    {
        return $this->data['store'] ?? 0;
    }

    /**
     * @return int
     */
    public function getWebsite() : int
    {
        return $this->data['website'] ?? 0;
    }

    /**
     * @return int
     */
    public function getCustomer() : int
    {
        return $this->data['customer'] ?? 0;
    }

    /**
     * @return array
     */
    public function getProducts() : array
    {
        return $this->data['products'];
    }
}
