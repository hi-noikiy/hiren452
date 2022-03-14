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

namespace Plumrocket\AutoInvoiceShipment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CreateInvoiceAfterShipment implements ObserverInterface
{
    /**
     * Data Helper
     *
     * @var \Plumrocket\AutoInvoiceShipment\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\AutoInvoiceShipment\Model\AutoInvoice
     */
    private $autoInvoice;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\ShipmentSender
     */
    private $shipmentSender;

    /**
     * SaveInvoiceAfter constructor.
     *
     * @param \Plumrocket\AutoInvoiceShipment\Model\AutoInvoice $autoInvoice
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
     * @param \Plumrocket\AutoInvoiceShipment\Helper\Data $dataHelper
     */
    public function __construct(
        \Plumrocket\AutoInvoiceShipment\Model\AutoInvoice $autoInvoice,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender,
        \Plumrocket\AutoInvoiceShipment\Helper\Data $dataHelper
    ) {
        $this->autoInvoice = $autoInvoice;
        $this->registry = $registry;
        $this->shipmentSender = $shipmentSender;
        $this->dataHelper = $dataHelper;
    }

    public function execute(Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();

        if (!$shipment->getOrder()->getIgnorePluginAndObserver()) {
            $this->registry->register('pr_auto_invoice_current_shipment', $shipment);
            if ($this->dataHelper->autoSendShipmentEmail()
                && !$shipment->getEmailSent()
                && !$this->dataHelper->isShipmentCreatedManually()
            ) {
                try {
                    $this->autoInvoice->log('OrderId: ' . $shipment->getOrder()->getId() . ' auto send shipment.');
                    $this->shipmentSender->send($shipment);
                } catch (\Exception $e) {
                    $this->autoInvoice->log($e->getMessage());
                }
            }

            if ($this->autoInvoice->validate($shipment->getOrder(), true)) {
                $this->autoInvoice->make($shipment->getOrder());
            }
        }
    }
}
