<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Search Autocomplete & Suggest
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Search\Setup;

class Uninstall extends \Plumrocket\Base\Setup\AbstractUninstall
{
    /**
     * Config section id
     *
     * @var string
     */
    protected $_configSectionId = 'prsearch';

    /**
     * Pathes to files
     *
     * @var array
     */
    protected $_pathes = ['/app/code/Plumrocket/Search'];

    /**
     * Tables Fields
     *
     * @var array
     */
    protected $_tablesFields = [
        'catalog_eav_attribute' => ['psearch_priority']
    ];

    /**
     * Tables
     *
     * @var array
     */
    protected $_tables = [];
}