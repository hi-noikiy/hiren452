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

namespace Plumrocket\AutoInvoiceShipment\Controller\Adminhtml\Invoicerules;

use Plumrocket\AutoInvoiceShipment\Controller\Adminhtml\Invoicerules;

class Save extends Invoicerules
{
    protected function _beforeSave($model, $request)
    {
        $data = $request->getParams();
        if (!empty($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }
        unset($data['rule']);

        if (is_array($data['website'])) {
            $data['websites'] = implode(',', $data['website']);
        } else {
            $data['websites'] = $data['website'];
        }
        unset($data['website']);

        if (is_array($data['customer_group'])) {
            $data['customer_groups'] = implode(',', $data['customer_group']);
        } else {
            $data['customer_groups'] = $data['customer_group'];
        }
        unset($data['customer_group']);

        $model->loadPost($data);
    }
}
