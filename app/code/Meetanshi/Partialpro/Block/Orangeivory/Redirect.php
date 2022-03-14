<?php

namespace Meetanshi\Partialpro\Block\Orangeivory;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Meetanshi\Partialpro\Model\PartialpaymentFactory;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Customer\Model\SessionFactory;
use Meetanshi\Partialpro\Helper\Data;
use Meetanshi\Partialpro\Model\InstallmentsFactory;

class Redirect extends Template
{
    protected $partialpaymentFactory;
    protected $priceHepler;
    protected $customer;
    protected $dataHelper;
    protected $installmentsFactory;

    public function __construct(
        Context $context,
        PartialpaymentFactory $partialpaymentFactory,
        SessionFactory $customer,
        Data $dataHelper,
        priceHelper $priceHepler,
        InstallmentsFactory $installmentsFactory
    )
    {

        $this->partialpaymentFactory = $partialpaymentFactory;
        $this->priceHepler = $priceHepler;
        $this->customer = $customer;
        $this->dataHelper = $dataHelper;
        $this->installmentsFactory = $installmentsFactory;
        parent::__construct($context);
    }

    public function getPaymentForm()
    {
        $inst_id = $this->getRequest()->getParam('inst_id');

        if ($inst_id != '') {
            $installmentArrId = explode("-", $inst_id);

            $amount = 0;
            $partialPaymentId = '';

            foreach ($installmentArrId as $installmentId) {

                $installment = $this->installmentsFactory->create()->load($installmentId);
                $partialPaymentId = $installment->getPartialPaymentId();
                $amount += $installment->getInstallmentAmount();
            }

            if ($partialPaymentId != '') {
                $partialPayment = $this->partialpaymentFactory->create()->load($partialPaymentId);
                $orderIncrementId = $partialPayment->getOrderId();
                $currency = $partialPayment->getCurrencyCode();
                $purchaseRef = $orderIncrementId . '-' . $inst_id;
                return $this->dataHelper->getPaymentForm($purchaseRef, $currency, $amount);
            }
        }
        return '';
    }
}