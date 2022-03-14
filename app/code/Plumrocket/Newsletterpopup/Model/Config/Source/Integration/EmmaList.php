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

namespace Plumrocket\Newsletterpopup\Model\Config\Source\Integration;

/**
 * Class EmmaList
 *
 * @package Plumrocket\Newsletterpopup\Model\Config\Source\Integration
 */
class EmmaList extends \Plumrocket\Newsletterpopup\Model\Config\Source\Integration\AbstractIntegrationList
{
    /**
     * @var \Plumrocket\Newsletterpopup\Model\Integration\Emma
     */
    private $integrationModel;

    /**
     * EmmaList constructor.
     * @param \Plumrocket\Newsletterpopup\Model\Integration\Emma $integrationModel
     */
    public function __construct(
        \Plumrocket\Newsletterpopup\Model\Integration\Emma $integrationModel
    ) {
        $this->integrationModel = $integrationModel;
    }

    /**
     * @return \Plumrocket\Newsletterpopup\Model\Integration\Emma
     */
    public function getModel()
    {
        return $this->integrationModel;
    }
}
