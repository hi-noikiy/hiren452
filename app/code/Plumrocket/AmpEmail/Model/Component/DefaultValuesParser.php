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

class DefaultValuesParser
{
    /**
     * @param array $componentConfig
     * @return array
     */
    public function execute(array $componentConfig) : array
    {
        $defaultValues = [];

        if (isset($componentConfig['parameters'])) {
            foreach ($componentConfig['parameters'] as $name => $parameter) {
                if (isset($parameter['type'])) {
                    $defaultValues[$name] = $this->parseDefaultValue($parameter);
                }
            }
        }

        return $defaultValues;
    }

    /**
     * @param array $parameter
     * @return mixed
     */
    private function parseDefaultValue(array $parameter)
    {
        if (isset($parameter['value'])) {
            return $parameter['value'];
        }

        if ('multiselect' === $parameter['type'] && isset($parameter['values'])) {
            return implode(',', array_keys($parameter['values']));
        }

        return null;
    }
}
