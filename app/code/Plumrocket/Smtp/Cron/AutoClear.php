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
 * @package     Plumrocket SMTP
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Smtp\Cron;

use Exception;

class AutoClear
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var
     */
    protected $helper;

    /**
     * @var \Plumrocket\Smtp\Model\ResourceModel\Log\CollectionFactory
     */
    protected $logFactory;

    /**
     * @var \Plumrocket\Smtp\Helper\Data
     */
    private $dataHelper;

    /**
     * AutoClear constructor.
     *
     * @param \Psr\Log\LoggerInterface                                   $logger
     * @param \Plumrocket\Smtp\Helper\Data                               $dataHelper
     * @param \Plumrocket\Smtp\Model\ResourceModel\Log\CollectionFactory $logFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Plumrocket\Smtp\Helper\Data $dataHelper,
        \Plumrocket\Smtp\Model\ResourceModel\Log\CollectionFactory $logFactory
    ) {
        $this->logger = $logger;
        $this->logFactory = $logFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        if (! $this->dataHelper->canRunAutoClear()) {
            return $this;
        }

        try {
            $this->logFactory->create()
                ->addFieldToFilter(
                    'sent_at',
                    ['lteq' => date('Y-m-d H:i:s', $this->dataHelper->getRemoveTime())]
                )->walk('delete');
        } catch (Exception $exception) {
            $this->logger->critical($exception);
        }

        return $this;
    }
}
