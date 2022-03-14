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
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Model;

/**
 * Interface IntegrationRepositoryInterface
 */
interface IntegrationRepositoryInterface
{
    /**
     * Retrieve cached object instance
     *
     * @param $integrationId
     * @return \Plumrocket\Newsletterpopup\Model\IntegrationInterface
     */
    public function get($integrationId);

    /**
     * @return \Plumrocket\Newsletterpopup\Model\IntegrationInterface[]
     */
    public function getList();
}
