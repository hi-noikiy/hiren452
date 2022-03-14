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
 * @package     Plumrocket AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AmpEmail\Setup;

/**
 * Class Uninstall
 * @codingStandardsIgnoreFile
 * @package Plumrocket\AmpEmail\Setup
 */
class Uninstall extends \Plumrocket\Base\Setup\AbstractUninstall
{
    /**
     * Config section id
     *
     * @var string
     */
    protected $_configSectionId = 'prampemail';

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
        \Plumrocket\AmpEmail\Model\ResourceModel\Security\VerifiedSender::MAIN_TABLE_NAME,
    ];

    /**
     * Tables Fields
     *
     * @var array
     */
    protected $_tablesFields = [
        'email_template' => [
            'pramp_email_enable',
            'pramp_email_content',
            'pramp_email_styles',
            'pramp_email_mode',
            'pramp_email_testing_method',
            'pramp_email_automatic_emails',
            'pramp_email_manual_email',
            'pramp_email_manual_order',
            'pramp_email_manual_send',
        ]
    ];

    /**
     * Pathes to files
     *
     * @var array
     */
    protected $_pathes = ['/app/code/Plumrocket/AmpEmail'];
}
