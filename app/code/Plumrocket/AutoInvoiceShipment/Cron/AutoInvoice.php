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
use Magento\Framework\Stdlib\DateTime\DateTime;
use Plumrocket\AutoInvoiceShipment\Model\AutoInvoice as AutoInvoiceModel;
use Plumrocket\AutoInvoiceShipment\Model\AutoShipment as AutoShipmentModel;

class AutoInvoice extends AbstractCron
{
    /**
     * AutoInvoice constructor.
     *
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param DateTime               $dateTime
     * @param Data                   $dataHelper
     * @param Invoicerules           $invoicerules
     * @param Shipmentrules          $shipmentrules
     * @param AutoInvoiceModel       $autoInvoice
     * @param AutoShipmentModel      $autoShipment
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        DateTime $dateTime,
        Data $dataHelper,
        AutoInvoiceModel $autoInvoice,
        AutoShipmentModel $autoShipment,
        Invoicerules $invoicerules,
        Shipmentrules $shipmentrules
    ) {
        $this->model = $autoInvoice;
        $this->rules = $invoicerules;
        $this->secondModel = $autoShipment;
        $this->secondRules = $shipmentrules;
        parent::__construct($orderCollectionFactory, $dateTime, $dataHelper);
    }
}
