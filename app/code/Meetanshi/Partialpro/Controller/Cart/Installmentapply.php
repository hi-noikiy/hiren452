<?php

namespace Meetanshi\Partialpro\Controller\Cart;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\QuoteFactory;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Framework\Serialize\SerializerInterface;

class Installmentapply extends Action\Action
{
    protected $resultPageFactory;
    protected $partialpaymentCron;
    protected $quoteFactory;
    protected $cart;
    protected $helper;
    private $serializer;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Cart $cart,
        Data $helper,
        QuoteFactory $quoteFactory,
        SerializerInterface $serializer
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->quoteFactory = $quoteFactory;
        $this->cart = $cart;
        $this->helper = $helper;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    public function execute()
    {
        $numOfInstallment = $this->getRequest()->getParam('installment_number');
        try {
            if ($numOfInstallment > 0) {
                $quoteId = $this->cart->getQuote()->getId();
                $quote = $this->quoteFactory->create()->load($quoteId);
                $items = $quote->getAllVisibleItems();

                foreach ($items as $item) {
                    if ($item->getId()) {

                        $installmentNumber = $numOfInstallment;
                        $additionalOptions = [];
//                        if ($additionalOption = $item->getOptionByCode('additional_options')) {
//                            $additionalOptions = $this->serializer->unserialize($additionalOption->getValue());
//                        }

                        $additionalOptions[] = [
                            'label' => 'No Of Installments',
                            'value' => $numOfInstallment
                        ];

                        if (sizeof($additionalOptions) > 0) {
                            $item->addOption(array(
                                'product_id' => $item->getProductId(),
                                'code' => 'additional_options',
                                'value' => $this->serializer->serialize($additionalOptions)
                            ));
                        }

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
                        $installmentFee =  $installmentFee * $item->getQty();
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

                        $item->setPartialInstallmentFee($installmentFee );
                        $item->setPartialInstallmentNo($installmentNumber);
                        $item->setPartialPayNow($firstInstallmentPrice );
                        $item->setPartialPayLater($remainingPaymentPrice );
                        $item->save();
                    }
                }
                $this->messageManager->addSuccessMessage(__('Installment Apply Successfully.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $this->_redirect('checkout/cart/');
    }
}
