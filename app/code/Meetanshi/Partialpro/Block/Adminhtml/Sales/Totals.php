<?php

namespace Meetanshi\Partialpro\Block\Adminhtml\Sales;

use Magento\Framework\View\Element\Template;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Directory\Model\Currency;

class Totals extends Template
{
    protected $dataHelper;
    protected $currency;

    public function __construct(
        Template\Context $context,
        Data $dataHelper,
        Currency $currency,
        array $data = []
    )
    {

        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->currency = $currency;
    }

    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }

    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();

        if ($this->getSource()->getPartialInstallmentFee() > 0) {
            $basePartialPaymentFee = $this->dataHelper->convertCurrency($this->getOrder()->getBaseCurrencyCode(), $this->getOrder()->getOrderCurrencyCode(), $this->getSource()->getPartialInstallmentFee());
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => 'partial_installment_fee',
                    'value' => $this->getSource()->getPartialInstallmentFee(),
                    'label' => $this->dataHelper->getPartialInstallmentFeeLabel(),
                    'base_value' => $basePartialPaymentFee,
                ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        }

        if ($this->getSource()->getPartialPayNow() > 0) {
            $basePartialPayNow = $this->dataHelper->convertCurrency($this->getOrder()->getBaseCurrencyCode(), $this->getOrder()->getOrderCurrencyCode(), $this->getSource()->getPartialPayNow());
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => 'partial_pay_now',
                    'value' => $this->getSource()->getPartialPayNow(),
                    'label' => $this->dataHelper->getAmtPayNowLabel(),
                    'base_value' => $basePartialPayNow,
                ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        }

        if ($this->getSource()->getPartialPayLater() > 0) {
            $basePartialPayLater = $this->dataHelper->convertCurrency($this->getOrder()->getBaseCurrencyCode(), $this->getOrder()->getOrderCurrencyCode(), $this->getSource()->getPartialPayLater());

            $total = new \Magento\Framework\DataObject(
                [
                    'code' => 'partial_pay_later',
                    'value' => $this->getSource()->getPartialPayLater(),
                    'label' => $this->dataHelper->getAmtPayLaterLabel(),
                    'base_value' => $basePartialPayLater,
                ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        }

        return $this;
    }
}
