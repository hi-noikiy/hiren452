<?php

namespace Meetanshi\Partialpro\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class PartialPayLater extends AbstractTotal
{
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemo->setPartialPayLater(0);
        $amount = $creditmemo->getOrder()->getPartialPayLater();
        $creditmemo->setPartialPayLater($amount);

        //$creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $creditmemo->getPartialPayLater());
        //$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $creditmemo->getPartialPayLater());

        return $this;
    }
}
