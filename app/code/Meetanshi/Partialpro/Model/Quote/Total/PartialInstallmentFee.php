<?php

namespace Meetanshi\Partialpro\Model\Quote\Total;

use Magento\Quote\Model\QuoteValidator;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;

class PartialInstallmentFee extends Total\AbstractTotal
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
        Total $total
    )
    {

        parent::collect($quote, $shippingAssignment, $total);

        if (!sizeof($shippingAssignment->getItems())) {
            return $this;
        }

        $quote->setPartialInstallmentFee(0);
        $total->setTotalAmount('partial_installment_fee', 0);
        $total->setBaseTotalAmount('partial_installment_fee', 0);

        $enabled = $this->helperData->isModuleEnabled();

        $this->helperData->setAllQuoteValues($quote->getId());

        if ($quote->getIsMultiShipping()) {
            $this->helperData->setAllMultiShipping($shippingAssignment->getItems());
        }

        $getPartialProductSet = $this->helperData->getPartialProductSet($quote->getId());
        $installmentFeeEnable = $this->helperData->getPartialInstallmentFeeEnabled();
        $minimumOrderAmount = $this->helperData->getMinimumOrderAmount();

        $subtotal = $total->getTotalAmount('subtotal');

        if ($installmentFeeEnable && $getPartialProductSet && $enabled && $minimumOrderAmount <= $subtotal) {

            if ($quote->getIsMultiShipping()) {
                $fee = $this->helperData->getPartialMultiFee($shippingAssignment->getItems());
            } else {
                $fee = $this->helperData->getPartialInstallmentFee($quote->getId());
            }

            $basePartialInstallmentFee = $this->helperData->convertCurrency($quote->getBaseCurrencyCode(), $quote->getQuoteCurrencyCode(), $fee);

            $total->setTotalAmount('partial_installment_fee', $fee);
            $total->setBaseTotalAmount('partial_installment_fee', $basePartialInstallmentFee);
            $quote->setPartialInstallmentFee($fee);
            $total->setPartialInstallmentFee($fee);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
            $version = (float)$productMetadata->getVersion();

            if ($version > 2.1) {
            } else {
                $total->setGrandTotal($total->getGrandTotal() + $fee);
            }
        }else{
            $total->setTotalAmount('partial_installment_fee', 0);
            $total->setBaseTotalAmount('partial_installment_fee', 0);
            $quote->setPartialInstallmentFee(0);
            $total->setPartialInstallmentFee(0);
        }
        return $this;
    }

    public function fetch(Quote $quote, Total $total)
    {
        $enabled = $this->helperData->isModuleEnabled();
        $minimumOrderAmount = $this->helperData->getMinimumOrderAmount();
        $subtotal = $quote->getSubtotal();
        $fee = $total->getPartialInstallmentFee();
        $installmentFeeEnable = $this->helperData->getPartialInstallmentFeeEnabled();

        $result = [];
        if ($installmentFeeEnable && $enabled && ($minimumOrderAmount <= $subtotal) && $fee) {
            $result = [
                'code' => 'partial_installment_fee',
                'title' => $this->helperData->getPartialInstallmentFeeLabel(),
                'value' => $fee
            ];
        }
        return $result;
    }

    protected function clearValues(Total $total)
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
