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

namespace Plumrocket\AmpEmail\Observer;

class ResetLocatorsObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Plumrocket\AmpEmail\Api\ComponentLocatorPoolInterface
     */
    private $componentLocatorPool;

    /**
     * ResetLocatorsObserver constructor.
     *
     * @param \Plumrocket\AmpEmail\Api\ComponentLocatorPoolInterface $componentLocatorPool
     */
    public function __construct(\Plumrocket\AmpEmail\Api\ComponentLocatorPoolInterface $componentLocatorPool)
    {
        $this->componentLocatorPool = $componentLocatorPool;
    }

    /**
     * Don't need check if module enabled
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        foreach ($this->componentLocatorPool->getList() as $locator) {
            $locator->resetData();
        }
    }
}
