<?php

namespace Meetanshi\Partialpro\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class PartialInstallmentFee extends AbstractTotal
{
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemo->setPartialInstallmentFee(0);

        $amount = $creditmemo->getOrder()->getPartialInstallmentFee();
        $creditmemo->setPartialInstallmentFee($amount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getPartialInstallmentFee());
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getPartialInstallmentFee());

        return $this;
    }
}
