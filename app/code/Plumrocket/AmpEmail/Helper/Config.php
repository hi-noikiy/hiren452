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

namespace Plumrocket\AmpEmail\Helper;

/**
 * Class Config
 *
 * @package Plumrocket\AmpEmail\Helper
 */
class Config extends \Plumrocket\Base\Helper\Base
{
    /**
     * @var int
     */
    const AMP_EMAIL_DISABLED = 0;

    /**
     * @var int
     */
    const AMP_EMAIL_ENABLED = 1;

    /**
     * @var int
     */
    const AMP_TYPE = 3;

    /**
     * @param null $store
     * @return bool
     */
    public function isAllowRequestFromAmpPlayground($store = null) : bool
    {
        return false;
    }
}
