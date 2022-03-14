<?php

namespace Unific\Connector\Helper\Data;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;
use Unific\Connector\Helper\Filter;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

class Order
{
    /**
     * @var Filter
     */
    protected $filterHelper;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $dataObjectConverter;
    /**
     * @var OrderInterface|Order
     */
    protected $order;
    /**
     * @var Invoice
     */
    protected $invoice;
    /**
     * @var Shipment
     */
    protected $shipment;
    /**
     * @var array
     */
    protected $returnData = [];

    /**
     * OrderPlugin constructor.
     * @param Filter $filterHelper
     * @param Session $customerSession
     * @param ExtensibleDataObjectConverter $dataObjectConverter
     */
    public function __construct(
        Filter $filterHelper,
        Session $customerSession,
        ExtensibleDataObjectConverter $dataObjectConverter
    ) {
        $this->filterHelper = $filterHelper;
        $this->customerSession = $customerSession;
        $this->dataObjectConverter = $dataObjectConverter;
    }

    /**
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
        $this->setOrderInfo();
    }

    /**
     * @param InvoiceInterface $invoice
     */
    public function setInvoice(InvoiceInterface $invoice)
    {
        $this->invoice = $invoice;
        $this->setOrder($invoice->getOrder());
    }

    /**
     * @param ShipmentInterface $shipment
     */
    public function setShipment(ShipmentInterface $shipment)
    {
        $this->shipment = $shipment;
        $this->setOrder($shipment->getOrder());
    }

    /**
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return void
     */
    protected function setOrderInfo()
    {
        $this->returnData = $this->order->getData();

        $this->returnData['order_items'] = [];
        foreach ($this->order->getAllItems() as $item) {
            $itemData = $item->getData();

            $itemData['free_shipping']
                = (isset($itemData['free_shipping']) && $itemData['free_shipping'] == true) ? 1 : 0;

            if (isset($itemData['is_qty_decimal'])) {
                $itemData['is_qty_decimal'] = (int)$itemData['is_qty_decimal'];
            }

            if (isset($itemData['qty_ordered'])) {
                $itemData['qty_ordered'] = (int)$itemData['qty_ordered'];
            }

            if (isset($itemData['qty_canceled'])) {
                $itemData['qty_canceled'] = (int)$itemData['qty_canceled'];
            }

            if (isset($itemData['qty_invoiced'])) {
                $itemData['qty_invoiced'] = (int)$itemData['qty_invoiced'];
            }

            if (isset($itemData['qty_refunded'])) {
                $itemData['qty_refunded'] = (int)$itemData['qty_refunded'];
            }

            if (isset($itemData['qty_shipped'])) {
                $itemData['qty_shipped'] = (int)$itemData['qty_shipped'];
            }

            $this->returnData['order_items'][] = array_intersect_key(
                $itemData,
                array_flip($this->filterHelper->getItemsWhitelist())
            );
        }

        $this->returnData['addresses'] = [];

        if ($this->order->getBillingAddress() !== null) {
            $this->returnData['addresses']['billing'] = $this->dataObjectConverter->toFlatArray(
                $this->order->getBillingAddress(),
                [],
                \Magento\Sales\Api\Data\OrderAddressInterface::class
            );
            $this->returnData['addresses']['billing'] = $this->filterHelper->fixAddressKey(
                $this->returnData['addresses']['billing'],
                'street'
            );
        }

        if ($this->order->getShippingAddress() !== null) {
            $this->returnData['addresses']['shipping'] = $this->dataObjectConverter->toFlatArray(
                $this->order->getShippingAddress(),
                [],
                \Magento\Sales\Api\Data\OrderAddressInterface::class
            );
            $this->returnData['addresses']['shipping'] = $this->filterHelper->fixAddressKey(
                $this->returnData['addresses']['shipping'],
                'street'
            );
        }

        $this->returnData['payment'] = $this->order->getPayment()->getData();
    }

    /**
     * @return array
     */
    public function getOrderInfo()
    {
        // Sanitize order
        $this->returnData = array_intersect_key(
            $this->returnData,
            array_flip($this->filterHelper->getOrderWhiteList())
        );

        if (isset($this->returnData['customer_is_guest'])) {
            $this->returnData['customer_is_guest'] = (int)$this->returnData['customer_is_guest'];
        }

        // Sanitize order payment
        if (isset($this->returnData['payment'])) {
            $this->returnData['payment'] = array_intersect_key(
                $this->returnData['payment'],
                array_flip($this->filterHelper->getPaymentWhitelist())
            );
        }

        $itemsCount = count($this->returnData['order_items']);
        for ($i = 0; $i < $itemsCount; $i++) {
            $this->returnData['order_items'][$i] = array_intersect_key(
                $this->returnData['order_items'][$i],
                array_flip($this->filterHelper->getItemsWhitelist())
            );
        }

        if ($this->invoice) {
            $this->returnData['updated_at'] = $this->invoice->getUpdatedAt();
        }
        if ($this->shipment) {
            $shippingCarrier = explode('_', $this->order->getShippingMethod());
            $this->returnData['shipment'] = [
                'shipping_carrier' => array_key_exists(0, $shippingCarrier) ? $shippingCarrier[0] : null,
                'shipping_method' => array_key_exists(1, $shippingCarrier) ? $shippingCarrier[1] : null,
                'shipping_name' => $this->order->getShippingDescription()
            ];
            if ($this->shipment->getTracks()) {
                $this->returnData['shipment']['tracking'] = [];
                foreach ($this->shipment->getTracks() as $tracking) {
                    $this->returnData['shipment']['tracking'][] = [
                        'number' => $tracking->getTrackNumber(),
                        'created_at' => $tracking->getCreatedAt(),
                        'carrier_code' => $tracking->getCarrierCode(),
                        'carrier_title' => $tracking->getTitle()
                    ];
                }
            }
            $this->returnData['updated_at'] = $this->shipment->getUpdatedAt();
        }

        $this->returnData['shipments'] = [];
        if ($this->order->hasShipments()) {
            foreach ($this->order->getShipmentsCollection() as $shipment) {
                $this->returnData['shipments'][] = $this->getShipmentData($shipment);
            }
        }

        return $this->filterHelper->sanitizeAddressData($this->returnData);
    }

    /**
     * @param ShipmentInterface $shipment
     * @return array
     */
    protected function getShipmentData(ShipmentInterface $shipment)
    {
        $shipmentData = $this->dataObjectConverter->toFlatArray(
            $shipment,
            [],
            ShipmentInterface::class
        );

        if ($this->shipment->getTracks()) {
            $shipmentData['tracking'] = [];
            foreach ($this->shipment->getTracks() as $tracking) {
                $shipmentData['tracking'][] = [
                    'number' => $tracking->getTrackNumber(),
                    'created_at' => $tracking->getCreatedAt(),
                    'carrier_code' => $tracking->getCarrierCode(),
                    'carrier_title' => $tracking->getTitle()
                ];
            }
        }

        return $shipmentData;
    }
}
