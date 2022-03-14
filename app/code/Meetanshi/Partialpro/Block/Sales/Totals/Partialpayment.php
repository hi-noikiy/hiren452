<?php

namespace Meetanshi\Partialpro\Block\Sales\Totals;

use Magento\Framework\View\Element\Template;
use Meetanshi\Partialpro\Helper\Data;

class Partialpayment extends Template
{
    protected $dataHelper;
    protected $_order;
    protected $_source;

    public function __construct(
        Template\Context $context,
        Data $dataHelper,
        array $data = []
    )
    {

        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    public function displayFullSummary()
    {
        return true;
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function getStore()
    {
        return $this->_order->getStore();
    }

    public function getOrder()
    {
        return $this->_order;
    }

    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        if ($this->_source->getPartialInstallmentFee() > 0) {
            $installmentFee = new \Magento\Framework\DataObject(
                [
                    'code' => 'partial_installment_fee',
                    'strong' => false,
                    'value' => $this->_source->getPartialInstallmentFee(),
                    'label' => $this->dataHelper->getPartialInstallmentFeeLabel(),
                ]
            );
            $parent->addTotal($installmentFee, 'partial_installment_fee');
        }

        if ($this->_source->getPartialPayNow() > 0) {
            $partialAmtNow = new \Magento\Framework\DataObject(
                [
                    'code' => 'partial_pay_now',
                    'strong' => false,
                    'value' => $this->_source->getPartialPayNow(),
                    'label' => $this->dataHelper->getAmtPayNowLabel(),
                ]
            );
            $parent->addTotal($partialAmtNow, 'partial_pay_now');
        }

        if ($this->_source->getPartialPayLater() > 0) {
            $partialAmtLater = new \Magento\Framework\DataObject(
                [
                    'code' => 'partial_pay_later',
                    'strong' => false,
                    'value' => $this->_source->getPartialPayLater(),
                    'label' => $this->dataHelper->getAmtPayLaterLabel(),
                ]
            );
            $parent->addTotal($partialAmtLater, 'partial_pay_later');
        }

        return $this;
    }
}
