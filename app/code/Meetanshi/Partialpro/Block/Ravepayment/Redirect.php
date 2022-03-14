<?php

namespace Meetanshi\Partialpro\Block\Ravepayment;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Meetanshi\Partialpro\Model\PartialpaymentFactory;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Customer\Model\SessionFactory;
use Meetanshi\Partialpro\Helper\Data;
use Meetanshi\Partialpro\Model\InstallmentsFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Redirect extends Template
{
    protected $partialpaymentFactory;
    protected $priceHepler;
    protected $customer;
    protected $dataHelper;
    protected $installmentsFactory;
    protected $scopeConfig;
    protected $scopeStore;

    public function __construct(
        Context $context,
        PartialpaymentFactory $partialpaymentFactory,
        SessionFactory $customer,
        Data $dataHelper,
        priceHelper $priceHepler,
        ScopeConfigInterface $scopeConfig,
        InstallmentsFactory $installmentsFactory
    )
    {

        $this->partialpaymentFactory = $partialpaymentFactory;
        $this->priceHepler = $priceHepler;
        $this->customer = $customer;
        $this->dataHelper = $dataHelper;
        $this->installmentsFactory = $installmentsFactory;
        $this->scopeConfig = $scopeConfig;
        $this->scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        parent::__construct($context);
    }

    public function getAllData()
    {
        $data = [];
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

                $test = 1;
                if ($this->scopeConfig->getValue('payment/rave/test_mode', $this->scopeStore)) {
                    $public_key = $this->scopeConfig->getValue('payment/rave/pb_key', $this->scopeStore);
                } else {
                    $public_key = $this->scopeConfig->getValue('payment/rave/live_pb_key', $this->scopeStore);
                    $test = 0;
                }

                $data['order_currency'] = $currency;
                //$data['order_currency'] = 'NGN';
                $data['txref'] = $purchaseRef;
                $data['amount'] = $amount;
                $data['custom_description'] = $this->scopeConfig->getValue('payment/rave/modal_desc', $this->scopeStore);
                $data['custom_logo'] = $this->scopeConfig->getValue('payment/rave/logo', $this->scopeStore);
                $data['custom_title'] = $this->scopeConfig->getValue('payment/rave/modal_title', $this->scopeStore);
                $data['customer_email'] = $partialPayment->getCustomerEmail();
                $data['PBFPubKey'] = $public_key;
                $data['redirect_url'] = $this->getUrl('partialpayment/ravepayment/accept');
                $data['cancel_url'] = $this->getUrl('partialpayment/account/view') . 'profile/' . $partialPayment->getId();
                $data['test'] = $test;

            }
        }
        return $data;
    }
}