<?php

namespace Meetanshi\Partialpro\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class PartialPayNow extends AbstractTotal
{
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemo->setPartialPayNow(0);
        $amount = $creditmemo->getOrder()->getPartialPayNow();
        $creditmemo->setPartialPayNow($amount);
        return $this;
    }
}
