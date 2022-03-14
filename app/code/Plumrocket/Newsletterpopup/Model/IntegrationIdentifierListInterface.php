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
 * Interface IntegrationIdentifierListInterface
 */
interface IntegrationIdentifierListInterface
{
    /**
     * Check if integration identifier is valid
     *
     * @param $integrationIdentifier
     * @return bool
     */
    public function isValid($integrationIdentifier);

    /**
     * Retrieve class name of integration
     *
     * @param $integrationIdentifier
     * @return false|string
     */
    public function getIntegrationClass($integrationIdentifier);

    /**
     * Retrieve array of integration identifiers
     *
     * @return array
     */
    public function getIntegrationIdentifiers();
}
