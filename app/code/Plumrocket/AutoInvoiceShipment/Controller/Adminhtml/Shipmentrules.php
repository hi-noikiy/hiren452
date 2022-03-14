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

namespace Plumrocket\AutoInvoiceShipment\Controller\Adminhtml;

use Plumrocket\Base\Controller\Adminhtml\Actions;

class Shipmentrules extends Actions
{
    const ADMIN_RESOURCE = 'Plumrocket_AutoInvoiceShipment::shipment_rules';

    protected $_formSessionKey  = 'prautoinvoiceshipment_form_data';

    protected $_modelClass      = 'Plumrocket\AutoInvoiceShipment\Model\Shipmentrules';
    protected $_activeMenu      = 'Plumrocket_AutoInvoiceShipment::shipment_rules';
    protected $_objectTitle     = 'Auto Shipment Rule';
    protected $_objectTitles    = 'Auto Shipment Rules';

    protected $_statusField     = 'status';
}
