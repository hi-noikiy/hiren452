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

class AppendCommentToEmail
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            Invoicerules::APPEND_COMMENT_TO_EMAIL_NO    => __('No'),
            Invoicerules::APPEND_COMMENT_TO_EMAIL_YES   => __('Yes'),
        ];
    }
}
