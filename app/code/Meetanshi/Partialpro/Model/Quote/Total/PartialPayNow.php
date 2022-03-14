<?php

namespace Meetanshi\Partialpro\Model\Quote\Total;

use Magento\Quote\Model\QuoteValidator;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;

class PartialPayNow extends Quote\Address\Total\AbstractTotal
{

    protected $helperData;
    protected $quoteValidator = null;

    public function __construct(
        QuoteValidator $quoteValidator,
        Data $helperData
    )
    {
        $this->quoteValidator = $quoteValidator;
        $this->helperData = $helperData;
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Quote\Address\Total $total
    )
    {

        parent::collect($quote, $shippingAssignment, $total);
        if (!sizeof($shippingAssignment->getItems())) {
            return $this;
        }
        $quote->setPartialPayNow(0);
        $quote->setPartialMaxInstallment(0);

        $enabled = $this->helperData->isModuleEnabled();
        $minimumOrderAmount = $this->helperData->getMinimumOrderAmount();
        $subtotal = $total->getTotalAmount('subtotal');
        $getPartialProductSet = $this->helperData->getPartialProductSet($quote->getId());

        if($quote->getIsMultiShipping()){
            $partialAmtNow = $this->helperData->calculatePartialMultiPaynow($shippingAssignment->getItems());
            $getMaxInstallments = $this->helperData->getMaxMultiInstallments($shippingAssignment->getItems());
        }else{
            $partialAmtNow = $this->helperData->calculatePartialPaynow($quote->getId());
            $getMaxInstallments = $this->helperData->getMaxInstallments($quote->getId());
        }

        if ($enabled && $getPartialProductSet && $minimumOrderAmount <= $subtotal && $getMaxInstallments) {
            if ($this->helperData->getPartialShippingTexInclude()) {
                $partialAmtNow = $partialAmtNow + (($total->getShippingAmount() + $total->getTaxAmount()) / $getMaxInstallments);
            } else {
                $partialAmtNow = $partialAmtNow + ($total->getShippingAmount() + $total->getTaxAmount());
            }

            $total->setPartialPayNow($partialAmtNow);
            $quote->setPartialPayNow($partialAmtNow);
            $total->setPartialMaxInstallment($getMaxInstallments);
            $quote->setPartialMaxInstallment($getMaxInstallments);
        }else{
            $total->setPartialPayNow(0);
            $quote->setPartialPayNow(0);
            $total->setPartialMaxInstallment(0);
            $quote->setPartialMaxInstallment(0);
        }
        return $this;
    }

    public function fetch(Quote $quote, Quote\Address\Total $total)
    {
        $enabled = $this->helperData->isModuleEnabled();
        $minimumOrderAmount = $this->helperData->getMinimumOrderAmount();
        $subtotal = $quote->getSubtotal();
        $partialAmtNow = $total->getPartialPayNow();
        $result = [];
        if ($enabled && ($minimumOrderAmount <= $subtotal) && $partialAmtNow) {
            $result = [
                'code' => 'partial_pay_now',
                'title' => $this->helperData->getAmtPayNowLabel(),
                'value' => $partialAmtNow
            ];
        }
        return $result;
    }


    protected function clearValues(Quote\Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
}
