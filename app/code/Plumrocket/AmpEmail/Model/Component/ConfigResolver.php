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

namespace Plumrocket\AmpEmail\Model\Component;

class ConfigResolver implements \Plumrocket\AmpEmail\Api\ComponentConfigResolverInterface
{
    /**
     * @var \Plumrocket\AmpEmail\Model\Component\Config\Data
     */
    private $dataStorage;

    /**
     * ConfigResolver constructor.
     *
     * @param \Plumrocket\AmpEmail\Model\Component\Config\Data $dataStorage
     */
    public function __construct(\Plumrocket\AmpEmail\Model\Component\Config\Data $dataStorage)
    {
        $this->dataStorage = $dataStorage;
    }

    /**
     * @param string $type
     * @return array
     */
    public function execute(string $type) : array
    {
        $widgets = $this->dataStorage->get();

        foreach ($widgets as $widget) {
            if (isset($widget['@']['type']) && $type === $widget['@']['type']) {
                return $widget;
            }
        }

        return [];
    }
}
