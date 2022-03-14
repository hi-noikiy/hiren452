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

namespace Plumrocket\AmpEmail\Model\Email;

use Plumrocket\AmpEmail\Model\Email\Old\AmpMessage as OldAmpMessage;

class AmpMessageFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Plumrocket\AmpEmail\Model\Magento\VersionProvider
     */
    private $versionProvider;

    /**
     * @var \Plumrocket\AmpEmail\Model\Smtp\ModuleList
     */
    private $smtpModuleList;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * AmpMessageFactory constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface          $objectManager
     * @param \Plumrocket\AmpEmail\Model\Magento\VersionProvider $versionProvider
     * @param \Plumrocket\AmpEmail\Model\Smtp\ModuleList         $smtpModuleList
     * @param \Psr\Log\LoggerInterface                           $logger
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Plumrocket\AmpEmail\Model\Magento\VersionProvider $versionProvider,
        \Plumrocket\AmpEmail\Model\Smtp\ModuleList $smtpModuleList,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->objectManager = $objectManager;
        $this->versionProvider = $versionProvider;
        $this->smtpModuleList = $smtpModuleList;
        $this->logger = $logger;
    }

    /**
     * Create instance depend on magento version
     *
     * @param array $data
     * @return AmpMessageInterface
     */
    public function create(array $data = []) : AmpMessageInterface
    {
        if ($this->versionProvider->isMagentoVersionBelow('2.2.8') || $this->smtpModuleList->isOnlyZendOne()) {
            $this->logger->debug('AMP Email: Old Amp Message created');
            return $this->objectManager->create(OldAmpMessage::class, $data); //@codingStandardsIgnoreLine
        }

        return $this->objectManager->create(AmpMessage::class, $data); //@codingStandardsIgnoreLine
    }
}
