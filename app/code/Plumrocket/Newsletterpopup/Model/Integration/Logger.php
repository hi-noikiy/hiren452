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

namespace Plumrocket\Newsletterpopup\Model\Integration;

/**
 * Class Logger
 */
class Logger extends \Monolog\Logger
{
    /**
     * Name of integration instance
     *
     * @var string
     */
    private $integrationName = 'Integration';

    /**
     * Set name for Integration
     *
     * @param $name
     * @return $this
     */
    public function setIntegrationName($name)
    {
        $name = trim($name);

        if (! empty($name)) {
            $this->integrationName = $name;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRecord($level, $message, array $context = [])
    {
        try {
            $levelName = $this->getLevelName($level);
        } catch (\Exception $e) {
            $levelName = $level;
        }

        $message = sprintf(
            '%s response %s: %s',
            mb_strtoupper($this->integrationName),
            $levelName,
            $message
        );

        return parent::addRecord($level, $message, $context);
    }
}
