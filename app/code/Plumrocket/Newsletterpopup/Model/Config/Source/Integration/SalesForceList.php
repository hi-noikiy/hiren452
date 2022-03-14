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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Model\Config\Source\Integration;

/**
 * Class SalesForceList
 *
 * @package Plumrocket\Newsletterpopup\Model\Config\Source\Integration
 */
class SalesForceList extends \Plumrocket\Newsletterpopup\Model\Config\Source\Integration\AbstractIntegrationList
{
    /**
     * @var \Plumrocket\Newsletterpopup\Model\Integration\SalesForceList
     */
    private $integrationModel;

    /**
     * CampaignMonitorList constructor.
     * @param \Plumrocket\Newsletterpopup\Model\Integration\SalesForce $integrationModel
     */
    public function __construct(
        \Plumrocket\Newsletterpopup\Model\Integration\SalesForce $integrationModel
    ) {
        $this->integrationModel = $integrationModel;
    }

    /**
     * @return \Plumrocket\Newsletterpopup\Model\Integration\SalesForceList
     */
    public function getModel()
    {
        return $this->integrationModel;
    }
}
