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

class ConfigList
{
    /**
     * @var \Plumrocket\AmpEmail\Model\Component\Config\Data
     */
    private $dataStorage;

    /**
     * ConfigList constructor.
     *
     * @param \Plumrocket\AmpEmail\Model\Component\Config\Data $dataStorage
     */
    public function __construct(\Plumrocket\AmpEmail\Model\Component\Config\Data $dataStorage)
    {
        $this->dataStorage = $dataStorage;
    }

    /**
     * @param array $filters
     * @return array
     */
    public function execute(array $filters = []) : array
    {
        $widgets = $this->dataStorage->get();

        return $this->filterByParams($widgets, $filters);
    }

    /**
     * @param array $widgets
     * @param array $filters
     * @return array
     */
    private function filterByParams(array $widgets, array $filters) : array
    {
        $result = $widgets;

        if (! empty($filters)) {
            foreach ($widgets as $code => $widget) {
                foreach ($filters as $field => $value) {
                    if (!isset($widget[$field]) || (string)$widget[$field] != $value) {
                        unset($result[$code]);
                        break;
                    }
                }
            }
        }

        return $result;
    }
}
