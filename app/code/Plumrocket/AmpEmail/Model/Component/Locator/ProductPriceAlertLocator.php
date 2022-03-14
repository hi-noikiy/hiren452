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

use Plumrocket\AmpEmail\Api\ComponentPriceAlertLocatorInterface;

class ProductPriceAlertLocator extends \Magento\Framework\DataObject implements ComponentPriceAlertLocatorInterface
{
    /**
     * @return \Plumrocket\AmpEmail\Api\ComponentPriceAlertLocatorInterface
     */
    public function resetData() : \Plumrocket\AmpEmailApi\Model\LocatorInterface
    {
        return $this->unsetData();
    }

    /**
     * @param int $productId
     * @return int|float
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getInitialPrice(int $productId)
    {
        if ($price = $this->_getData($productId)) {
            return $price;
        }

        throw new \Magento\Framework\Exception\NotFoundException(
            __('We cannot find initial price for product %1', $productId)
        );
    }

    /**
     * @param int       $productId
     * @param int|float $price
     * @return mixed
     */
    public function setInitialPrice(int $productId, $price) : ComponentPriceAlertLocatorInterface
    {
        return $this->setData($productId, $price);
    }
}
