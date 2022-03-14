<?php

namespace Meetanshi\Partialpro\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class PartialPayLater extends AbstractTotal
{
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setPartialPayLater(0);
        $amount = $invoice->getOrder()->getPartialPayLater();
        $invoice->setPartialPayLater($amount);

        //$invoice->setGrandTotal($invoice->getGrandTotal() - $invoice->getPartialPayLater());
        //$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $invoice->getPartialPayLater());

        return $this;
    }
}