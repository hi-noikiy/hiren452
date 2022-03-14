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

namespace Plumrocket\AmpEmail\Model\Security;

/**
 * Class AmpEmailTokenType
 * @since 1.1.0
 */
class AmpEmailTokenType implements \Plumrocket\Token\Api\TypeInterface
{
    const KEY = 'amp_email';

    /**
     * @return string
     */
    public function getKey() : string
    {
        return self::KEY;
    }

    /**
     * @return int
     */
    public function getLifetime() : int
    {
        return strtotime("{$this->getLifetimeDays()} day", 0);
    }

    /**
     * Life time 31 days because it's google requirement
     * @link https://developers.google.com/gmail/ampemail/authenticating-requests
     *
     * @return int
     */
    public function getLifetimeDays() : int
    {
        return 31;
    }
}
