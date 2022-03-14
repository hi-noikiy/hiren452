<?php
/**
 * A Magento 2 module named Experius/EmailCatcher
 * Copyright (C) 2019 Experius
 *
 * This file included in Experius/EmailCatcher is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Experius\EmailCatcher\Cron;

use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class Clean
{
    const DAYS_TO_CLEAN = 30;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Clean constructor.
     *
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resourceConnection
    ) {
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->connection = $this->resourceConnection->getConnection();
    }

    /**
     * Execute the cron
     *
     * @return int $deletionCount
     */
    public function execute()
    {
        $where = "created_at < '" . date('c', time() - (self::DAYS_TO_CLEAN * (3600 * 24))) . "'";

        $deletionCount = $this->connection->delete(
            $this->resourceConnection->getTableName('experius_emailcatcher'),
            $where
        );

        $this->logger->addInfo(__('Experius EmailCatcher Cleanup: Removed %1 records', $deletionCount));

        return (int)$deletionCount;
    }
}
