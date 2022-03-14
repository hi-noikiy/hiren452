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

namespace Plumrocket\AmpEmail\Model\Smtp;

class ExtendZendOneMailTransport
{
    /**
     * @var \Plumrocket\AmpEmail\Model\Email\Old\Zend\Transport\SmtpFactory
     */
    private $prampEmailOldZendTransportFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * ExtendZendOneMailTransport constructor.
     *
     * @param \Plumrocket\AmpEmail\Model\Email\Old\Zend\Transport\SmtpFactory $prampEmailOldZendTransportFactory
     * @param \Psr\Log\LoggerInterface                                        $logger
     */
    public function __construct(
        \Plumrocket\AmpEmail\Model\Email\Old\Zend\Transport\SmtpFactory $prampEmailOldZendTransportFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->prampEmailOldZendTransportFactory = $prampEmailOldZendTransportFactory;
        $this->logger = $logger;
    }

    /**
     * @param \Zend_Mail_Transport_Smtp $zendOneTransport
     * @return \Plumrocket\AmpEmail\Model\Email\Old\Zend\Transport\Smtp|\Zend_Mail_Transport_Smtp
     */
    public function execute(\Zend_Mail_Transport_Smtp $zendOneTransport)
    {
        try {
            $reflectionClass = new \ReflectionClass($zendOneTransport);

            $reflectionProperty = $reflectionClass->getProperty('_config');
            $reflectionProperty->setAccessible(true);
            $config = $reflectionProperty->getValue($zendOneTransport);

            $reflectionProperty = $reflectionClass->getProperty('_host');
            $reflectionProperty->setAccessible(true);
            $host = $reflectionProperty->getValue($zendOneTransport);

            /** @var \Plumrocket\AmpEmail\Model\Email\Old\Zend\Transport\Smtp $prampZendOneTransport */
            $prampZendOneTransport = $this->prampEmailOldZendTransportFactory->create(
                ['host' => $host, 'config' => $config]
            );

            $this->logger->debug('AMP Email: zend transport for Mageplaza_Smtp extended');

            return $prampZendOneTransport;
        } catch (\Exception $e) { //@codingStandardsIgnoreLine
            // do nothing
        }

        return $zendOneTransport;
    }
}
