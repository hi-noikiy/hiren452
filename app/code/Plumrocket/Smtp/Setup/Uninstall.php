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

namespace Plumrocket\Smtp\Setup;

class Uninstall extends \Plumrocket\Base\Setup\AbstractUninstall
{
    /**
     * Config section id
     *
     * @var string
     */
    protected $_configSectionId = 'prsmtp';

    /**
     * Pathes to files
     *
     * @var array
     */
    protected $_pathes = ['/app/code/Plumrocket/Smtp'];

    /**
     * Attributes
     *
     * @var array
     */
    protected $_attributes = [];

    /**
     * Tables
     *
     * @var array
     */
    protected $_tables = [
        'plumrocket_smtp_email_log'
    ];
}
