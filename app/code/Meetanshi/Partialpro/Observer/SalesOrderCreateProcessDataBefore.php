<?php

namespace Meetanshi\Partialpro\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Backend\Model\Session\Quote;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Framework\App\Request\Http;

class SalesOrderCreateProcessDataBefore implements ObserverInterface
{
    protected $sessionQuote;
    protected $request;
    protected $helper;
    private $serializer;

    public function __construct(
        Http $request,
        Data $dataHelper,
        Quote $sessionQuote,
        SerializerInterface $serializer
    )
    {
        $this->request = $request;
        $this->sessionQuote = $sessionQuote;
        $this->serializer = $serializer;
        $this->helper = $dataHelper;
    }

    public function execute(Observer $observer)
    {
        try {
            $postData = $this->request->getPost();

            if (!isset($postData['item'])) {
                return;
            }
            if (isset($postData['update_items']) && $postData['update_items']) {
                $quote = $this->sessionQuote->getQuote();

                if (isset($postData['installment_count_whole'])) {
                    foreach ($quote->getAllItems() as $id => $item) {
                        $additionalOptions = [];
                        if ($postData['installment_count_whole'] == 0) {

                            $item->addOption(array(
                                'product_id' => $item->getProductId(),
                                'code' => 'additional_options',
                                'value' => $this->serializer->serialize($additionalOptions)
                            ));

                            $item->setPartialInstallmentNo(0);
                            $item->setPartialApply(0);
                            $item->setPartialInstallmentFee(0);
                            $item->setPartialPayNow(0);
                            $item->setPartialPayLater(0);

                        } else {

                            $additionalOptions[] = [
                                'label' => 'No Of Installments',
                                'value' => $postData['installment_count_whole']
                            ];

                            $item->addOption(array(
                                'product_id' => $item->getProductId(),
                                'code' => 'additional_options',
                                'value' => $this->serializer->serialize($additionalOptions)
                            ));

                            $installmentNumber = $postData['installment_count_whole'];

                            $item->setPartialApply(1);


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
                            $installmentFee = $installmentFee * $item->getQty();
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

                            $item->setPartialInstallmentFee($installmentFee);
                            $item->setPartialInstallmentNo($installmentNumber);
                            $item->setPartialPayNow($firstInstallmentPrice);
                            $item->setPartialPayLater($remainingPaymentPrice);
                        }
                    }
                }

                if (isset($postData['installment_count'])) {
                    foreach ($quote->getAllItems() as $item) {
                        if (array_key_exists($item->getId(), $postData['installment_count'])) {
                            $installmentNumber = $postData['installment_count'][$item->getId()];

                            $additionalOptions = [];
                            if ($installmentNumber == 0) {

                                $item->addOption(array(
                                    'product_id' => $item->getProductId(),
                                    'code' => 'additional_options',
                                    'value' => $this->serializer->serialize($additionalOptions)
                                ));

                                $item->setPartialInstallmentNo(0);
                                $item->setPartialApply(0);
                                $item->setPartialInstallmentFee(0);
                                $item->setPartialPayNow(0);
                                $item->setPartialPayLater(0);

                            } else {

                                $additionalOptions[] = [
                                    'label' => 'No Of Installments',
                                    'value' => $installmentNumber
                                ];

                                $item->addOption(array(
                                    'product_id' => $item->getProductId(),
                                    'code' => 'additional_options',
                                    'value' => $this->serializer->serialize($additionalOptions)
                                ));


                                $item->setPartialApply(1);

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
                                $installmentFee = $installmentFee * $item->getQty();
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
                                $item->setPartialInstallmentFee($installmentFee);
                                $item->setPartialInstallmentNo($installmentNumber);
                                $item->setPartialPayNow($firstInstallmentPrice);
                                $item->setPartialPayLater($remainingPaymentPrice);
                            }
                        }
                    }
                }



            }
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());
        }
    }
}
