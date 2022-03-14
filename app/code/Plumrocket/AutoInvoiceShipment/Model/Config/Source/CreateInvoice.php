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

namespace Plumrocket\AutoInvoiceShipment\Model\Config\Source;

use Plumrocket\AutoInvoiceShipment\Model\Invoicerules;

class CreateInvoice
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            Invoicerules::CREATE_INVOICE_AFTER_CREATED => __('After order is created'),
            Invoicerules::CREATE_INVOICE_AFTER_SHIPPED => __('After shipment is created')
        ];
    }
}
