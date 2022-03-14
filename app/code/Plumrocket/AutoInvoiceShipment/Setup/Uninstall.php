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
 * @package   Plumrocket_AutoInvoiceShipment
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AutoInvoiceShipment\Setup;

use Plumrocket\Base\Setup\AbstractUninstall;

class Uninstall extends AbstractUninstall
{
    /**
     * Part of path in table `core_config_data`
     * @var string
     */
    protected $_configSectionId = 'prautoinvoiceshipment';

    /**
     * Files location
     * @var array
     */
    protected $_pathes = [
        '/app/code/Plumrocket/AutoInvoiceShipment',
        '/vendor/plumrocket/module-autoinvoiceshipment',
    ];

    /**
     * Tables for remove
     * @var array
     */
    protected $_tables =
    [
        'pl_autoinvoiceshipment_invoicerules',
        'pl_autoinvoiceshipment_shipmentrules',
    ];
}
