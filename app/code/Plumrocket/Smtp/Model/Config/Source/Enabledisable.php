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

namespace Plumrocket\Smtp\Model\Config\Source;

class Enabledisable extends AbstractSource
{
    /**
     * @var int
     */
    const ENABLED = 1;

    /**
     * @var int
     */
    const DISABLED = 0;

    /**
     * @return array
     */
    public function toOptionHash()
    {
        return [
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled')
        ];
    }
}
