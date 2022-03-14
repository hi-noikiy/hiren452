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

namespace Plumrocket\AutoInvoiceShipment\Cron;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Plumrocket\AutoInvoiceShipment\Helper\Data;
use Plumrocket\AutoInvoiceShipment\Model\Invoicerules;
use Plumrocket\AutoInvoiceShipment\Model\Shipmentrules;
use Magento\Sales\Model\Order;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Plumrocket\AutoInvoiceShipment\Model\AutoInvoice as AutoInvoiceModel;
use Plumrocket\AutoInvoiceShipment\Model\AutoShipment as AutoShipmentModel;
use Plumrocket\AutoInvoiceShipment\Model\ResourceModel\Invoicerules\Collection as InvoicerulesCollection;
use Plumrocket\AutoInvoiceShipment\Model\ResourceModel\Shipmentrules\Collection as ShipmentrulesCollection;

class AbstractCron
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var AutoInvoiceModel | AutoShipmentModel
     */
    protected $model;

    /**
     * @var AutoInvoiceModel | AutoShipmentModel
     */
    protected $secondModel;

    /**
     * @var Invoicerules | Shipmentrules
     */
    protected $rules;

    /**
     * @var Invoicerules | Shipmentrules
     */
    protected $secondRules;

    /**
     * @var InvoicerulesCollection | ShipmentrulesCollection
     */
    protected $secondRulesCollection;

    /**
     * AbstractCron constructor.
     *
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param DateTime               $dateTime
     * @param Data                   $dataHelper
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        DateTime $dateTime,
        Data $dataHelper
    ) {
        $this->orderCollectionFactory   = $orderCollectionFactory;
        $this->dateTime                 = $dateTime;
        $this->dataHelper               = $dataHelper;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $this->model->log('run execute');
        if (!$this->dataHelper->moduleEnabled() || !$this->model || !$this->rules) {
            $this->model->log('Module is disable');
            return false;
        }

        $this->model->log('Start cron');
        $time = $this->dateTime->timestamp() - 24 * 60 * 60;

        $rulesCollection = $this->rules->getActiveRules();
        if (!$rulesCollection->getSize()) {
            $this->model->log('All Rules is disable');
            return false;
        }

        $orderCollection = $this->orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(
                'state',
                ['in' => [
                Order::STATE_NEW,
                Order::STATE_PROCESSING,
                ]]
            )
            ->addFieldToFilter(
                'main_table.created_at',
                ['gt' => $this->dateTime->gmtDate('Y-m-d H:i:s', $time)]
            );

        foreach ($orderCollection as $order) {
            if ($this->model->validate($order, false, $rulesCollection) && $this->model->make($order)) {
                if ($this->secondModel->validate($order, false, $this->getSecondRulesCollection())) {
                    $this->secondModel->make($order);
                }
            }
        }

        $this->model->log('End cron');
        return true;
    }

    /**
     * Get second rules collection only if necessary
     * Save second rules collection in variable for optimization
     *
     * @return InvoicerulesCollection | ShipmentrulesCollection
     */
    protected function getSecondRulesCollection()
    {
        if ($this->secondRulesCollection === null) {
            $this->secondRulesCollection = $this->secondRules->getActiveRules();
        }
        return $this->secondRulesCollection;
    }
}
