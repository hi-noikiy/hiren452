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

interface ComponentPriceAlertLocatorInterface extends \Plumrocket\AmpEmailApi\Model\LocatorInterface
{
    /**
     * @return ComponentPriceAlertLocatorInterface
     */
    public function resetData() : \Plumrocket\AmpEmailApi\Model\LocatorInterface;

    /**
     * @param int $productId
     * @return int|float
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getInitialPrice(int $productId);

    /**
     * @param int       $productId
     * @param int|float $price
     * @return mixed
     */
    public function setInitialPrice(int $productId, $price) : ComponentPriceAlertLocatorInterface;
}
