<?php

namespace Meetanshi\Partialpro\Block\Tagpay;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Meetanshi\Partialpro\Model\PartialpaymentFactory;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Customer\Model\SessionFactory;
use Meetanshi\Partialpro\Helper\Tagpay;
use Meetanshi\Partialpro\Model\InstallmentsFactory;
use Magento\Sales\Api\Data\OrderInterfaceFactory;

class Redirect extends Template
{
    protected $partialpaymentFactory;
    protected $priceHepler;
    protected $customer;
    protected $dataHelper;
    protected $installmentsFactory;
    protected $orderFactory;

    public function __construct(
        Context $context,
        PartialpaymentFactory $partialpaymentFactory,
        SessionFactory $customer,
        Tagpay $dataHelper,
        priceHelper $priceHepler,
        OrderInterfaceFactory $orderFactory,
        InstallmentsFactory $installmentsFactory
    )
    {

        $this->partialpaymentFactory = $partialpaymentFactory;
        $this->priceHepler = $priceHepler;
        $this->customer = $customer;
        $this->dataHelper = $dataHelper;
        $this->installmentsFactory = $installmentsFactory;
        $this->orderFactory = $orderFactory;
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
                $order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);

                $currency = $partialPayment->getCurrencyCode();
                $purchaseRef = $orderIncrementId . '-' . $inst_id;
                return $this->dataHelper->getPaymentForm($order,$purchaseRef, $currency, $amount,1);
            }
        }
        return '';
    }
}