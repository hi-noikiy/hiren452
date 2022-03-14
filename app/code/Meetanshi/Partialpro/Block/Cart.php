<?php

namespace Meetanshi\Partialpro\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\AbstractProduct;
use Meetanshi\Partialpro\Helper\Data;

class Cart extends AbstractProduct
{
    protected $helperData;

    public function __construct(
        Context $context,
        Data $helperData,
        array $data
    )
    {

        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    public function getIsPartialPayment()
    {
        $enabled = $this->helperData->isModuleEnabled();
        if ($enabled) {
            return 1;
        }
        return 0;
    }

    public function getIsFlexyLaywayPlan()
    {
        return $this->helperData->getIsFlexyLaywayPlan();
    }

    public function getAllowedForCustomerGrp()
    {
        return $this->helperData->getAllowedForCustomerGrp();
    }

    public function getFormAction()
    {
        return $this->getUrl('partialpayment/cart/installmentapply', ['_secure' => true]);
    }

    public function getInstallmentCount()
    {
        $installmentNumber = $this->helperData->getInstallmentNumber();
        return $installmentNumber;
    }

    public function getInstallmentTable()
    {
        $installmentNumber = $this->helperData->getInstallmentNumber();
        return $this->helperData->getInstallmentTableCart($installmentNumber);
    }

    public function getApplyPartialPaymentToWhole()
    {
        $val = $this->helperData->getApplyPartialPaymentToWhole();
        if ($val == 2) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getProductpageDescrition()
    {
        return $this->helperData->getProductpageDescrition();
    }

    public function getProductpageLabel()
    {
        return $this->helperData->getProductpageLabel();
    }

    public function getIsMinimumOrderAmount()
    {
        $minumumOrderAmount = $this->helperData->getMinimumOrderAmount();
        $subTotal = $this->helperData->cart->getQuote()->getSubtotal();

        if ($subTotal > $minumumOrderAmount) {
            return 1;
        }
        return 0;
    }
}