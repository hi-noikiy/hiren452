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

namespace Plumrocket\AmpEmail\Plugin\Magento\ProductAlert\Model;

class ObserverPlugin
{
    /**
     * @var \Plumrocket\AmpEmail\Model\Component\ProductAlert\GetInitialPrices
     */
    private $getSubscribedPrices;

    /**
     * @var \Plumrocket\AmpEmail\Model\Component\ProductAlert\CurrentAlertsDataLocator
     */
    private $currentAlertsDataLocators;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * ObserverPlugin constructor.
     *
     * @param \Plumrocket\AmpEmail\Model\Component\ProductAlert\GetInitialPrices         $getSubscribedPrices
     * @param \Plumrocket\AmpEmail\Model\Component\ProductAlert\CurrentAlertsDataLocator $currentAlertsDataLocators
     * @param \Psr\Log\LoggerInterface                                                   $logger
     */
    public function __construct(
        \Plumrocket\AmpEmail\Model\Component\ProductAlert\GetInitialPrices $getSubscribedPrices,
        \Plumrocket\AmpEmail\Model\Component\ProductAlert\CurrentAlertsDataLocator $currentAlertsDataLocators,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->getSubscribedPrices = $getSubscribedPrices;
        $this->currentAlertsDataLocators = $currentAlertsDataLocators;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\ProductAlert\Model\Observer $subject
     */
    public function beforeProcess(\Magento\ProductAlert\Model\Observer $subject)
    {
        try {
            $this->currentAlertsDataLocators->setAlertsData($this->getSubscribedPrices->execute());
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
