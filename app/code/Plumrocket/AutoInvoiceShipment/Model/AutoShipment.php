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

namespace Plumrocket\AutoInvoiceShipment\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Plumrocket\AutoInvoiceShipment\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Sales\Model\Order\ShipmentFactory;
use function GuzzleHttp\Promise\queue;

class AutoShipment extends AutoAbstract
{
    /**
     * Shipment rules model
     *
     * @var Shipmentrules
     */
    protected $shipmentRules;

    /**
     * Necessary for make shipment
     *
     * @var ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * Use for send email
     *
     * @var ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var SourceItem
     */
    protected $sourceItem;

    /**
     * AutoShipment constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param Transaction $transactionSave
     * @param Data $dataHelper
     * @param StoreManagerInterface $storeManager
     * @param Shipmentrules $shipmentRules
     * @param ShipmentFactory $shipmentFactory
     * @param ShipmentSender $shipmentSender
     * @param SourceItem $sourceItem
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        OrderCollectionFactory $orderCollectionFactory,
        Transaction $transactionSave,
        Data $dataHelper,
        StoreManagerInterface $storeManager,
        Shipmentrules $shipmentRules,
        ShipmentFactory $shipmentFactory,
        ShipmentSender $shipmentSender,
        SourceItem $sourceItem,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->shipmentRules    = $shipmentRules;
        $this->shipmentFactory  = $shipmentFactory;
        $this->shipmentSender   = $shipmentSender;
        $this->sourceItem       = $sourceItem;

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
     * Create shipment by Order
     *
     * @param Order $order
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function make($order)
    {
        try {
            $this->log('Order #' . $order->getId() . ' is valid for Autosippment.');
            $order->setIgnorePluginAndObserver(true);
            $shipment = $this->shipmentFactory->create($order, $this->getItemsToShip($order));

            if (!$shipment->getTotalQty()) {
                $this->log('No item qty for shipment Order ID: ' . $order->getId());
                return false;
            }

            $comment = $this->getComment();
            $useNotification = $this->useCustomerNotification();

            if (! empty($comment) && $useNotification) {
                // add comment to email
                $shipment->setCustomerNote($comment);
                $shipment->setCustomerNoteNotify(true);
            }

            $this->log('Start register ship for Order #' . $order->getId());
            $shipment->register();
            $order = $shipment->getOrder();

            if (! empty($comment)) {
                $order->addStatusToHistory(
                    false,
                    $comment,
                    $useNotification
                );
                $order->save();
            }

            $this->saveShipment($shipment);

            if ($this->dataHelper->autoSendShipmentEmail() && !$shipment->getEmailSent()) {
                $this->log('OrderId: ' . $order->getId() . ' send shipment.');
                $this->shipmentSender->send($shipment);
            }
            return true;
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        } catch (\Error $e) {
            $this->log($e->getMessage());
        }

        $this->log('End Autosippment.');
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
            $this->log('Autoshippment is disabled.');
            return false;
        }

        if (!$order->canShip()) {
            $this->log('Cannot Ship Order #' . $order->getId());
            return false;
        }

        if ($rulesCollection === null) {
            $websiteId = $this->storeManager
                ->getStore($order->getStoreId())
                ->getWebsiteId();
            $rulesCollection = $this->shipmentRules->getActiveRules($websiteId);
        }

        if (!$rulesCollection || !$rulesCollection->getSize()) {
            $this->log('No one Autosippment rule is found for Order #' . $order->getId());
            return false;
        }

        foreach ($rulesCollection as $rule) {
            /**
             * @var $rule Shipmentrules
             */
            if ($rule->preValidate($order, $skipValidCreateAfter) && $rule->validate($order)) {
                $this->currentRule = $rule;
                return true;
            }
        }

        return false;
    }

    /**
     * @param $order
     * @return array
     */
    protected function getItemsToShip($order)
    {
        $invoiceQty = (int) $order->getInvoiceCollection()->count();
        $qtys = [];
        $items = [];
        $websiteId = $order->getStore()->getWebsiteId();
        // Get qty from last Invoice
        if ($invoiceQty > 1) {
            /**
             * @var $invoice \Magento\Sales\Model\Order\Invoice
             */
            $invoice = $order->getInvoiceCollection()->getLastItem();
            foreach ($invoice->getAllItems() as $item) {
                /**
                 * @var $item \Magento\Sales\Model\Order\Invoice\Item
                 */
                $qtys[$item->getOrderItem()->getId()] = $item->getQty();
            }
        }

        foreach ($order->getAllItems() as $orderItem) {
            // Check if order item is virtual
            if ($orderItem->getIsVirtual()) {
                continue;
            }

            if ($invoiceQty
                && ($orderItem->getQtyInvoiced() != $orderItem->getQtyShipped())
                && $orderItem->getProductType() !== BundleType::TYPE_CODE
            ) {
                if ($invoiceQty !== 1) {
                    $qtyShipped = isset($qtys[$orderItem->getId()]) ? $qtys[$orderItem->getId()] : 0;
                } else {
                    $qtyShipped = $orderItem->getQtyInvoiced();
                }
            } else {
                $qtyShipped = $this->getQtyFromOrderItem($orderItem, (bool)$invoiceQty);
            }

            $qty = min($qtyShipped, $orderItem->getSimpleQtyToShip());
            $product = $orderItem->getProduct();

            if ($this->dataHelper->isSingleSourceMode()) {
                $productQty = $product->getExtensionAttributes()->getStockItem()->getQty();
            } else {
                $productQty = $this->sourceItem->getProductQty($product, $websiteId);
            }

            if (null !== $productQty && $productQty < $qty) {
                $qty = $productQty;
            }

            $items[$orderItem->getParentItemId() ?: $orderItem->getId()] = $qty;
        }

        return $items;
    }

    /**
     * Get item quantity to Shipment, used only for cron
     *
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param boolean                         $hasInvoice
     * @return int
     */
    protected function getQtyFromOrderItem($orderItem, $hasInvoice)
    {
        if ($orderItem->getProductType() === BundleType::TYPE_CODE) {
            $qty = $this->getQtyForBundle($orderItem, $hasInvoice);
        } else {
            if ($this->currentRule->getCreateShipment() == Shipmentrules::CREATE_AFTER_INVOICE_CREATED) {
                $qty = $orderItem->getQtyInvoiced() - $orderItem->getQtyShipped();
            } else {
                $qty = $orderItem->getSimpleQtyToShip();
            }
        }
        return ($qty > 0) ? $qty : 0;
    }

    /**
     * Get item quantity to Shipment for Bundle, used only for cron
     * If order has invoice - partial mode
     *
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param boolean                         $partialMode
     * @return int
     */
    protected function getQtyForBundle($orderItem, $partialMode)
    {
        $this->log('Get quantity for Bundle');
        if (!$partialMode && $this->currentRule->getCreateShipment() == Shipmentrules::CREATE_AFTER_ORDER_CREATED) {
            return $orderItem->getQtyToShip();
        }

        $parentQty = $orderItem->getQtyOrdered();
        $qtys = [];

        foreach ($orderItem->getChildrenItems() as $item) {
            /** @var \Magento\Sales\Model\Order\Item $item */
            if ($partialMode) {
                $bundleQtyRequire = $item->getQtyOrdered() / $parentQty;
                $qtys[] = floor($item->getQtyInvoiced() / $bundleQtyRequire - $orderItem->getQtyShipped());
            } else {
                if ($item->getQtyInvoiced() != $item->getQtyOrdered()) {
                    return 0;
                }
            }
        }

        if ($partialMode) {
            $this->log('Quantities by children: ' . json_encode($qtys));
            $min = min($qtys);
            if (!$min) {
                return 0;
            } elseif ($min != $orderItem->getQtyShipped()) {
                return $min - $orderItem->getQtyShipped();
            }
        }

        return $orderItem->getSimpleQtyToShip();
    }

    /**
     * Save shipment
     *
     * @param $shipment
     * @return $this
     * @throws \Exception
     */
    protected function saveShipment($shipment)
    {
        $shipment->getOrder()->setIsInProcess(true);
        if (null === $shipment->getItems()) {
            $shipment->setItems($shipment->getAllItems());
        }

        $this->transactionSave->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();

        return $this;
    }

    /**
     * @return string
     */
    protected function getComment()
    {
        if (! $this->currentRule) {
            return (string) $this->dataHelper->getMassActionShipmentComment();
        }

        return parent::getComment();
    }

    /**
     * @return bool
     */
    protected function useCustomerNotification(): bool
    {
        if (! $this->currentRule) {
            return (bool) $this->dataHelper->isMassActionShipmentEmailEnabled();
        }

        return parent::useCustomerNotification();
    }
}
