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

namespace Plumrocket\AutoInvoiceShipment\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Plumrocket\AutoInvoiceShipment\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Invoice;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Bundle\Model\Product\Type as BundleType;

class AutoInvoice extends AutoAbstract
{
    /**
     * @var Invoicerules
     */
    protected $invoiceRules;

    /**
     * @var InvoiceService
     */
    protected $orderService;

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * AutoInvoice constructor.
     *
     * @param Context                    $context
     * @param Registry                   $registry
     * @param OrderCollectionFactory     $orderCollectionFactory
     * @param Transaction                $transactionSave
     * @param Data                       $dataHelper
     * @param StoreManagerInterface      $storeManager
     * @param Invoicerules               $invoiceRules
     * @param InvoiceManagementInterface $orderService
     * @param InvoiceSender              $invoiceSender
     * @param AbstractResource|null      $resource
     * @param AbstractDb|null            $resourceCollection
     * @param array                      $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        OrderCollectionFactory $orderCollectionFactory,
        Transaction $transactionSave,
        Data $dataHelper,
        StoreManagerInterface $storeManager,
        Invoicerules $invoiceRules,
        InvoiceManagementInterface $orderService,
        InvoiceSender $invoiceSender,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->invoiceRules     = $invoiceRules;
        $this->orderService     = $orderService;
        $this->invoiceSender    = $invoiceSender;
        parent::__construct(
            $context,
            $registry,
            $orderCollectionFactory,
            $transactionSave,
            $dataHelper,
            $storeManager,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Create invoice by Order
     *
     * @param Order $order
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function make($order)
    {
        try {
            $this->log('Order #' . $order->getId() . ' is valid for Autoinvoice.');
            $order->setIgnorePluginAndObserver(true);
            $qtys = $this->getQuantitiesForInvoice($order);
            $this->log('Qty for invoice: ' . json_encode($qtys));

            if (false === $qtys) {
                $this->log('No item qty for invoice Order ID: ' . $order->getId());

                return false;
            }

            $invoice = $this->orderService->prepareInvoice($order, $qtys);
            $invoice->setRequestedCaptureCase($this->currentRule
                ? $this->currentRule->getCaptureCase()
                : $this->dataHelper->getMassActionCaptureAmount());

            $comment = $this->getComment();
            $notifyCustomer = $this->useCustomerNotification();

            if (! empty($comment) && $notifyCustomer) {
                // add comment to email
                $invoice->setCustomerNote($comment);
                $invoice->setCustomerNoteNotify(true);
            }

            $this->log('Start register invoice for Order #' . $order->getId());
            $invoice->register();
            $order = $invoice->getOrder();

            if (! empty($comment)) {
                $order->addStatusToHistory(
                    false,
                    $comment,
                    $notifyCustomer
                );

                $order->save();
            }

            // save invoice
            $this->saveInvoice($invoice);

            if ($this->dataHelper->autoSendInvoiceEmail() && !$invoice->getEmailSent()) {
                $this->invoiceSender->send($invoice);
            }

            return true;
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        } catch (\Error $e) {
            $this->log($e->getMessage());
        }

        $this->log('End Autoinvoice.');
        return false;
    }

    /**
     * @param $order
     * @param $skipValidCreateAfter
     * @param null $rulesCollection
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate($order, $skipValidCreateAfter, $rulesCollection = null): bool
    {
        if (!($order instanceof Order) || !$this->dataHelper->moduleEnabled($order->getStoreId())) {
            $this->log('Autoinvoice is disabled.');
            return false;
        }

        if (!$order->canInvoice()) {
            $this->log('Cannot Invoice Order #' . $order->getId());
            return false;
        }

        if ($rulesCollection === null) {
            $websiteId = $this->storeManager
                ->getStore($order->getStoreId())
                ->getWebsiteId();
            $rulesCollection = $this->invoiceRules->getActiveRules($websiteId);
        }

        if (!$rulesCollection || !$rulesCollection->getSize()) {
            $this->log('No one Autoinvoice rule is found for Order #' . $order->getId());
            return false;
        }

        if (!$this->_registry->registry('ed_obeserver_disable')) {
            $this->_registry->register('ed_obeserver_disable', 1);
        }

        foreach ($rulesCollection as $rule) {
            /* @var $rule Invoicerules */
            if ($rule->preValidate($order, $skipValidCreateAfter) && $rule->validate($order)) {
                $this->currentRule = $rule;

                return true;
            }
        }

        return false;
    }

    /**
     * Save invoice
     *
     * @param  Invoice $invoice
     * @return $this
     */
    protected function saveInvoice($invoice)
    {
        $invoice->getOrder()->setIsInProcess(true);
        $this->transactionSave->addObject(
            $invoice
        )->addObject(
            $invoice->getOrder()
        )->save();

        return $this;
    }

    /**
     * Get individually qty for all items
     *
     * @param $order Order
     * @return array | bool
     */
    protected function getQuantitiesForInvoice($order)
    {
        if ($this->currentRule && $this->currentRule->getCreateInvoice() === Invoicerules::CREATE_INVOICE_AFTER_CREATED
            && !$this->dataHelper->isShipmentCreatedManually()
        ) {
            return [];
        }

        $qtys = [];
        $shipmentSaveInOrder = true;
        /**
         * @var $shipment \Magento\Sales\Model\Order\Shipment | null
         */
        $shipment = null;

        // Get Shipment from registry
        if (null !== $this->_registry->registry('pr_auto_invoice_current_shipment')
            && $this->_registry->registry('pr_auto_invoice_current_shipment')->getOrderId() == $order->getId()
        ) {
            $shipment = $this->_registry->registry('pr_auto_invoice_current_shipment');
            $shipmentSaveInOrder = false;
            // Get last Shipment from Order
        } elseif ($order->hasShipments()) {
            $shipmentCollection = $order->getShipmentsCollection();
            if ($shipmentCollection) {
                $shipment = $shipmentCollection->getLastItem();
            }
        }

        if ($shipment) {
            foreach ($shipment->getAllItems() as $item) {
                /**
                 * @var $item \Magento\Sales\Model\Order\Shipment\Item
                 */
                if (0 == $item->getQty()) {
                    continue;
                }

                $orderItem = $item->getOrderItem();

                if ($orderItem->getProductType() === BundleType::TYPE_CODE) {
                    $qtys += $this->getQtyForBundle($orderItem, $item);
                } else {
                    $qtys[$orderItem->getId()] = $shipmentSaveInOrder
                        ? $this->getQtyFromOrderItem($orderItem)
                        : min($item->getQty(), $orderItem->getQtyToInvoice());
                }
            }
        }

        if ($this->_registry->registry('pr_auto_invoice_current_shipment') !== null) {
            $this->_registry->unregister('pr_auto_invoice_current_shipment');
        }

        return (!empty($qtys) && empty(array_filter($qtys))) ? false : $qtys;
    }

    /**
     * Get item quantity to Invoice, used only for cron
     *
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return int
     */
    protected function getQtyFromOrderItem($orderItem)
    {
        if ($this->currentRule
            && $this->currentRule->getCreateInvoice() == Invoicerules::CREATE_INVOICE_AFTER_SHIPPED
        ) {
            $qty = $orderItem->getQtyShipped() - $orderItem->getQtyInvoiced();
        } else {
            $qty = $orderItem->getQtyToInvoice();
        }

        return ($qty > 0) ? $qty : 0;
    }

    /**
     * Get items quantity to Invoice for Bundle children
     *
     * @param \Magento\Sales\Model\Order\Item          $orderItem
     * @param \Magento\Sales\Model\Order\Shipment\Item $shipmentItem
     * @return array
     */
    protected function getQtyForBundle($orderItem, $shipmentItem)
    {
        $qtys = [];
        foreach ($orderItem->getChildrenItems() as $item) {
            /** @var \Magento\Sales\Model\Order\Item $item */
            $qtys[$item->getId()] = min(
                (int)$item->getQtyToInvoice(),
                (int)$item->getQtyOrdered()/$orderItem->getQtyOrdered()*$shipmentItem->getQty()
            );
        }

        return $qtys;
    }

    /**
     * @return string
     */
    protected function getComment()
    {
        if (! $this->currentRule) {
            return (string) $this->dataHelper->getMassActionInvoiceComment();
        }

        return parent::getComment();
    }

    /**
     * @return bool
     */
    protected function useCustomerNotification(): bool
    {
        if (! $this->currentRule) {
            return (bool) $this->dataHelper->isMassActionInvoiceEmailEnabled();
        }

        return parent::useCustomerNotification();
    }
}
