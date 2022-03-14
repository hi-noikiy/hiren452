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

namespace Plumrocket\AutoInvoiceShipment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Invoice;

class SaveInvoiceAfter implements ObserverInterface
{
    /**
     * AutoShipment model
     *
     * @var \Plumrocket\AutoInvoiceShipment\Model\AutoShipment
     */
    protected $autoShipment;

    /**
     * InvoiceSender
     *
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;

    /**
     * Data Helper
     *
     * @var \Plumrocket\AutoInvoiceShipment\Helper\Data
     */
    protected $dataHelper;

    /**
     * SaveInvoiceAfter constructor.
     *
     * @param \Plumrocket\AutoInvoiceShipment\Model\AutoShipment    $autoShipment
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param \Plumrocket\AutoInvoiceShipment\Helper\Data           $dataHelper
     */
    public function __construct(
        \Plumrocket\AutoInvoiceShipment\Model\AutoShipment $autoShipment,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Plumrocket\AutoInvoiceShipment\Helper\Data $dataHelper
    ) {
        $this->autoShipment     = $autoShipment;
        $this->invoiceSender    = $invoiceSender;
        $this->dataHelper       = $dataHelper;
    }

    /**
     * Create shipment after save invoice
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getData('event/order');
        $invoice = $order->getPayment()->getCreatedInvoice();

        if ($invoice instanceof Invoice && !$invoice->getOrder()->getIgnorePluginAndObserver()) {
            if ($this->dataHelper->autoSendInvoiceEmail()
                && !$invoice->getEmailSent()
                && !$this->dataHelper->isInvoiceCreatedManually()
                && $invoice->getState() === Invoice::STATE_PAID
                && $invoice->getIncrementId()
            ) {
                try {
                    $this->autoShipment->log('OrderId: ' . $order->getId() . ' auto send invoice.');
                    $this->invoiceSender->send($invoice);
                } catch (\Exception $e) {
                    $this->autoShipment->log($e->getMessage());
                }
            }

            if ($this->autoShipment->validate($order, true)) {
                $this->autoShipment->make($order);
            }
        }
    }
}
