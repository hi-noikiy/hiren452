<?php

namespace Meetanshi\Partialpro\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Meetanshi\Partialpro\Helper\Data;

class SalesOrderInvoicePay implements ObserverInterface
{
    protected $helperData;

    public function __construct(
        Data $helperData
    )
    {
        $this->helperData = $helperData;
    }

    public function execute(EventObserver $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();

        $getPartialProductSet = $this->helperData->getPartialProductSet($order->getQuoteId());

        if ($getPartialProductSet) {

            $getInstallmentPaidAmount = $this->helperData->getInstallmentPaidAmount($order->getIncrementId());
            $getBaseInstallmentPaidAmount = $this->helperData->convertCurrency($order->getBaseCurrencyCode(),$order->getOrderCurrencyCode(), $getInstallmentPaidAmount);

            $order->setTotalPaid($getInstallmentPaidAmount);
            $order->setBaseTotalPaid($getBaseInstallmentPaidAmount);
        }
    }
}