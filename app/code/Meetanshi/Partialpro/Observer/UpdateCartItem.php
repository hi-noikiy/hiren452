<?php

namespace Meetanshi\Partialpro\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\RequestInterface;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Framework\Serialize\SerializerInterface;

class UpdateCartItem implements ObserverInterface
{
    protected $layout;
    protected $storeManager;
    protected $request;
    protected $helper;
    private $serializer;

    public function __construct(
        StoreManagerInterface $storeManager,
        LayoutInterface $layout,
        RequestInterface $request,
        Data $data,
        SerializerInterface $serializer
    )
    {
        $this->layout = $layout;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->helper = $data;
        $this->serializer = $serializer;
    }

    public function execute(EventObserver $observer)
    {
        if ($this->helper->isModuleEnabled()) {

            $items = $observer->getCart()->getQuote()->getItems();

            foreach ($items as $item) {

                $isPartialApply = $item->getPartialApply();

                if ($isPartialApply) {
                    $installmentNumber = $item->getPartialInstallmentNo();

                    $firstInstallmentPrice = $otherInstallmentPrice = $installmentFee = $remainingPaymentPrice = $downPaymentPrice = 0;
                    $productId = (int)$item->getProductId();
                    $product = $this->helper->getProductById($productId);
                    $mainProductPrice = (($item->getPrice() * $item->getQty()) - $item->getDiscountAmount());
                    $finalPrice = $this->helper->convertPrice($mainProductPrice);

                    $configFee = $this->helper->getConfigInstallmentFee();
                    $configFeeCalculation = $this->helper->getInstallmentFeeCalculation();
                    $configDownPayment = $this->helper->getConfigDownPayment();
                    $configDownCalculation = $this->helper->getConfigDownCalculation();

                    if ($product->getDownPayment() && $product->getCalculationDownPayment() && $product->getApplyPartialPayment()) {
                        $downPayment = $product->getDownPayment();
                        $downCalculation = $product->getCalculationDownPayment();
                    } else {
                        $downPayment = $configDownPayment;
                        $downCalculation = $configDownCalculation;
                    }

                    if ($downCalculation == 2) {
                        $firstInstallmentPrice = $finalPrice * $downPayment / 100;
                    } else {
                        $firstInstallmentPrice = $downPayment;
                    }
                    $remainingPaymentPrice = $finalPrice - $firstInstallmentPrice;

                    if ($product->getInstallmentFee() && $product->getCalInstammentFeePayment() && $product->getApplyPartialPayment()) {
                        $installmentFee = $product->getInstallmentFee();
                        $calculateInstallmentFeePayment = $product->getCalInstammentFeePayment();
                    } else {
                        $installmentFee = $configFee;
                        $calculateInstallmentFeePayment = $configFeeCalculation;
                    }

                    if ($calculateInstallmentFeePayment == 2) {
                        $installmentFee = ($mainProductPrice * $installmentFee) / 100;
                    }
                    $installmentFee =  $installmentFee* $item->getQty();
                    if ($this->helper->getPartialInstallmentFeeEnabled()) {
                        if ($this->helper->getPartialInstallmentFeeInFirstInstallments()) {
                            $firstInstallmentPrice = $firstInstallmentPrice + $installmentFee;
                        } else if ($this->helper->getPartialInstallmentFeeInAllInstallments()) {
                            $firstInstallmentPrice = $firstInstallmentPrice + $installmentFee;
                            $remainingPaymentPrice = $remainingPaymentPrice + ($installmentFee * ($installmentNumber - 1));
                            $installmentFee = $installmentFee * ($installmentNumber);
                        }
                    } else {
                        $installmentFee = 0;
                    }

                    $installmentFee = $this->helper->convertPrice($installmentFee);

                    $item->setPartialInstallmentFee($installmentFee);
                    $item->setPartialInstallmentNo($installmentNumber);
                    $item->setPartialPayNow($firstInstallmentPrice );
                    $item->setPartialPayLater($remainingPaymentPrice );

                } else {

                    $productId = (int)$item->getProductId();
                    $product = $this->helper->getProductById($productId);
                    $finalPrice = $product->getFinalPrice();

                    $item->setPartialInstallmentFee(0);
                    $item->setPartialInstallmentNo(0);
                    $item->setPartialPayNow($finalPrice * $item->getQty());
                    $item->setPartialPayLater(0);
                }
            }
        }
    }
}
