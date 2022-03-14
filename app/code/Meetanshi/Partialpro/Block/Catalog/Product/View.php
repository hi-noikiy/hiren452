<?php

namespace Meetanshi\Partialpro\Block\Catalog\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\AbstractProduct;
use Meetanshi\Partialpro\Helper\Data;

class View extends AbstractProduct
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
            if ($this->getShowOnProductPage() == 0) {
                $product = $this->_coreRegistry->registry('current_product');
                $value = $product->getData('apply_partial_payment');
                return $value;
            } elseif ($this->getShowOnProductPage() == 1) {
                return 1;
            }
        }
        return 0;
    }

    public function getShowOnProductPage()
    {
        return $this->helperData->getShowOnProductPage();
    }

    public function getProductId()
    {
        $product = $this->_coreRegistry->registry('current_product');
        return $product->getId();
    }

    public function getIsFlexyLaywayPlan()
    {
        return $this->helperData->getIsFlexyLaywayPlan();
    }

    public function getAmtPayNowLabel()
    {
        return $this->helperData->getAmtPayNowLabel();
    }

    public function getAmtPayLaterLabel()
    {
        return $this->helperData->getAmtPayLaterLabel();
    }

    public function getProductpageLabel()
    {
        return $this->helperData->getProductpageLabel();
    }

    public function getProductpageDescrition()
    {
        return $this->helperData->getProductpageDescrition();
    }

    public function getAllowedForCustomerGrp()
    {
        return $this->helperData->getAllowedForCustomerGrp();
    }

    public function getInstallmentCount()
    {
        $product = $this->_coreRegistry->registry('current_product');
        $applyPartialPamment = $product->getData('apply_partial_payment');
        if ($applyPartialPamment) {
            $installmentNumber = $product->getData('no_installment');
        } else {
            $installmentNumber = $this->helperData->getInstallmentNumber();
        }
        if($installmentNumber=='' || $installmentNumber==NULL){
            $installmentNumber = $this->helperData->getInstallmentNumber();
        }

        return $installmentNumber;
    }

    public function getInstallmentTable()
    {
        $product = $this->_coreRegistry->registry('current_product');
        $applyPartialPamment = $product->getData('apply_partial_payment');
        if ($applyPartialPamment) {
            $installmentNumber = $product->getData('no_installment');
        } else {
            $installmentNumber = $this->helperData->getInstallmentNumber();
        }
        if($installmentNumber=='' || $installmentNumber==NULL){
            $installmentNumber = $this->helperData->getInstallmentNumber();
        }
        return $this->helperData->getInstallmentTable($product->getId(), $installmentNumber);
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
}
