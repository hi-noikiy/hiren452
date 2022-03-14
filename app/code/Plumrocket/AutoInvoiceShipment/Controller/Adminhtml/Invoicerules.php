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

class Invoicerules extends Actions
{
    const ADMIN_RESOURCE = 'Plumrocket_AutoInvoiceShipment::invoice_rules';

    protected $_formSessionKey  = 'prautoinvoiceshipment_form_data';

    protected $_modelClass      = 'Plumrocket\AutoInvoiceShipment\Model\Invoicerules';
    protected $_activeMenu      = 'Plumrocket_AutoInvoiceShipment::invoice_rules';
    protected $_objectTitle     = 'Auto Invoice Rule';
    protected $_objectTitles    = 'Auto Invoice Rules';

    protected $_statusField     = 'status';
}
