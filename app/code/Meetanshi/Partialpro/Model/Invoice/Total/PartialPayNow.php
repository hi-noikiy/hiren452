<?php

namespace Meetanshi\Partialpro\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class PartialPayNow extends AbstractTotal
{
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setPartialPayNow(0);
        $amount = $invoice->getOrder()->getPartialPayNow();
        $invoice->setPartialPayNow($amount);
        return $this;
    }
}
