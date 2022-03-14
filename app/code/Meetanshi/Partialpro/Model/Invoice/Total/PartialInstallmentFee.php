<?php

namespace Meetanshi\Partialpro\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Meetanshi\Partialpro\Helper\Data;

class PartialInstallmentFee extends AbstractTotal
{
    protected $helperData;

    public function __construct(Data $helperData)
    {
        $this->helperData = $helperData;
    }

    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setPartialInstallmentFee(0);
        $amount = $invoice->getOrder()->getPartialInstallmentFee();
        $invoice->setPartialInstallmentFee($amount);

        $getInstallmentPaidAmount = $amount;
        $getBaseInstallmentPaidAmount = $this->helperData->convertCurrency($invoice->getOrder()->getBaseCurrencyCode(), $invoice->getOrder()->getOrderCurrencyCode(), $getInstallmentPaidAmount);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $getInstallmentPaidAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $getBaseInstallmentPaidAmount);
        return $this;
    }
}
