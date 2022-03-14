<?php

namespace Meetanshi\Partialpro\Model\Quote\Total;

use Magento\Quote\Model\QuoteValidator;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;

class PartialPayLater extends Quote\Address\Total\AbstractTotal
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

        $quote->setPartialPayLater(0);
        $total->setTotalAmount('partial_pay_later', 0);
        $total->setBaseTotalAmount('partial_pay_later', 0);

        $enabled = $this->helperData->isModuleEnabled();
        $minimumOrderAmount = $this->helperData->getMinimumOrderAmount();
        $subtotal = $total->getTotalAmount('subtotal');
        $getPartialProductSet = $this->helperData->getPartialProductSet($quote->getId());


        if ($quote->getIsMultiShipping()) {
            $partialAmtLater = $this->helperData->calculatePartialMultiPaylater($shippingAssignment->getItems());
            $getMaxInstallments = $this->helperData->getMaxMultiInstallments($shippingAssignment->getItems());
        } else {
            $partialAmtLater = $this->helperData->calculatePartialPaylater($quote->getId());
            $getMaxInstallments = $this->helperData->getMaxInstallments($quote->getId());
        }

        if ($enabled && $getPartialProductSet && $minimumOrderAmount <= $subtotal && $getMaxInstallments) {
            if ($this->helperData->getPartialShippingTexInclude()) {
                $amtTaxandShipping = (($total->getShippingAmount() + $total->getTaxAmount()) / $getMaxInstallments);
                $partialAmtLater = $partialAmtLater + ($amtTaxandShipping * ($getMaxInstallments - 1));
            }

            $total->setPartialPayLater($partialAmtLater);
            $quote->setPartialPayLater($partialAmtLater);
        }else{
            $total->setPartialPayLater(0);
            $quote->setPartialPayLater(0);
        }
        return $this;
    }

    public function fetch(Quote $quote, Quote\Address\Total $total)
    {
        $enabled = $this->helperData->isModuleEnabled();
        $minimumOrderAmount = $this->helperData->getMinimumOrderAmount();
        $subtotal = $quote->getSubtotal();
        $partialAmtLater = $total->getPartialPayLater();

        $result = [];
        if ($enabled && ($minimumOrderAmount <= $subtotal) && $partialAmtLater) {
            $result = [
                'code' => 'partial_pay_later',
                'title' => $this->helperData->getAmtPayLaterLabel(),
                'value' => $partialAmtLater
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
