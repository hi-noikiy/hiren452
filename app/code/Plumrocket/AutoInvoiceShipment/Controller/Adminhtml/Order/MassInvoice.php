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
 * @copyright Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\AutoInvoiceShipment\Controller\Adminhtml\Order;

class MassInvoice extends AbstractMassAction
{
    /**
     * @inheritDoc
     */
    public function getSuccessMessage(int $successOrderCount)
    {
        return __('The invoice for %1 order(s) has been created.', $successOrderCount);
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessage(int $errorOrderCount)
    {
        return __('Cannot create invoice for %1 order(s).', $errorOrderCount);
    }
}
