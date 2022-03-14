<?php

namespace Meetanshi\Partialpro\Observer;

use Magento\Framework\Event\ObserverInterface;
use Meetanshi\Partialpro\Helper\Data;
use Meetanshi\Partialpro\Model\PartialpaymentFactory as Partialpayment;
use Meetanshi\Partialpro\Model\InstallmentsFactory as Installments;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as PartialInstallmentCollection;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Serialize\Serializer\Json as jsonHelper;

class AddMultiFeeToOrderObserver implements ObserverInterface
{
    protected $helperData;
    protected $partialpayment;
    protected $installments;
    protected $priceHepler;

    protected $inlineTranslation;
    protected $transportBuilder;
    protected $scopeConfig;
    protected $soapHandle;
    protected $partialInstallmentCollection;
    protected $jsonHelper;
    private $serializer;
    private $checkoutSession;

    public function __construct(
        Data $helperData,
        Partialpayment $partialpayment,
        Installments $installments,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $configScopeConfigInterface,
        TransportBuilder $transportBuilder,
        PartialInstallmentCollection $partialInstallmentCollection,
        priceHelper $priceHepler,
        SerializerInterface $serializer,
        Session $checkoutSession,
        jsonHelper $jsonHelper
    )
    {
        $this->helperData = $helperData;
        $this->partialpayment = $partialpayment;
        $this->installments = $installments;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $configScopeConfigInterface;
        $this->partialInstallmentCollection = $partialInstallmentCollection;
        $this->priceHepler = $priceHepler;
        $this->serializer = $serializer;
        $this->jsonHelper = $jsonHelper;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helperData->isModuleEnabled()) {
            $quote = $observer->getQuote();
            $multiAddress = $observer->getAddress();

            if ($this->helperData->getPartialProductSet($quote->getId())) {

                if ($multiAddress->getPartialPayNow() > 0 && $multiAddress->getPartialPayLater() > 0) {

                    $order = $observer->getOrder();

                    $partialOrderInfo = $quote->getPartialOrder();
                    if($partialOrderInfo==''){
                        $allOrderData = [$order->getIncrementId() => $multiAddress->getId()];
                        $jsonSerialize = $this->jsonHelper->serialize($allOrderData);
                        $quote->setPartialOrder($jsonSerialize);
                        $quote->save();

                    }else{
                        $allOrderData = $this->jsonHelper->unserialize($partialOrderInfo);
                        $allOrderData[$order->getIncrementId()] = $multiAddress->getId();
                        $jsonSerialize = $this->jsonHelper->serialize($allOrderData);
                        $quote->setPartialOrder($jsonSerialize);
                        $quote->save();
                    }


                    $order->setData('partial_installment_fee', $multiAddress->getPartialInstallmentFee());
                    $order->setData('partial_pay_now', $multiAddress->getPartialPayNow());
                    $order->setData('partial_pay_later', $multiAddress->getPartialPayLater());
                    $order->setData('partial_max_installment', $multiAddress->getPartialMaxInstallment());

                    foreach ($quote->getAllVisibleItems() as $quoteItem) {
                        $quoteItems[$quoteItem->getId()] = $quoteItem;
                    }

                    foreach ($order->getAllVisibleItems() as $orderItem) {
                        $quoteItemId = $orderItem->getQuoteItemId();
                        if (array_key_exists($quoteItemId, $quoteItems)) {
                            $quoteItem = $quoteItems[$quoteItemId];
                            $additionalOptions = $quoteItem->getOptionByCode('additional_options');
                            if (is_object($additionalOptions) && !(is_null($additionalOptions))) {
                                if ($additionalOptions->getOptionId() > 0) {
                                    $options = $orderItem->getProductOptions();
                                    $options['additional_options'] = $this->serializer->unserialize($additionalOptions->getValue());
                                    $orderItem->setProductOptions($options);
                                }
                            }
                        }
                    }

                    try {

                        $maxInstallment = $multiAddress->getPartialMaxInstallment();

                        $payment = $order->getPayment();
                        $method = $payment->getMethodInstance();

                        $customerProfileId = $this->checkoutSession->getCustomerProfileId();
                        $paymentProfileId = $this->checkoutSession->getPaymentProfileId();

                        $modelPartialpayment = $this->partialpayment->create();
                        $modelPartialpayment->setOrderId($order->getIncrementId())
                            ->setStoreId($order->getStoreId())
                            ->setCustomerId($order->getCustomerId())
                            ->setCustomerName($order->getCustomerFirstname() . '  ' . $order->getCustomerLastname())
                            ->setCustomerEmail($order->getCustomerEmail())
                            ->setOrderAmount($order->getGrandTotal())
                            ->setPaidAmount($multiAddress->getPartialPayNow())
                            ->setRemainingAmount($multiAddress->getPartialPayLater())
                            ->setTotalInstallments($maxInstallment)
                            ->setPaidInstallments(1)
                            ->setRemainingInstallments($maxInstallment - 1)
                            ->setPaymentStatus(1)
                            ->setCurrencyCode($order->getOrderCurrencyCode())
                            ->setAutoCaptureProfileId($customerProfileId)
                            ->setAutoCapturePaymentProfileId($paymentProfileId)
                            ->setSurchargeAmount($multiAddress->getPartialInstallmentFee());
                        $modelPartialpayment->save();

                        $currencycode = $order->getOrderCurrencyCode();

                        $installmentStatus = 2;
                        if ($method->getCode() == 'orangeivory' ||
                            $method->getCode() == 'tagpay' ||
                            $method->getCode() == 'mtn' ||
                            $method->getCode() == 'banktransfer' ||
                            $method->getCode() == 'cashondelivery' ||
                            $method->getCode() == 'checkmo') {
                            $installmentStatus = 1;
                        }
                        //else {
                        //   $order->setData('total_paid', $order->getTotalPaid() + $quote->getPartialPayNow());
                        //  $order->setData('total_due', $order->getTotalDue() - $quote->getPartialPayNow());
                        //}

                        if ($method->getCode() == 'ravepayment') {
                            $getAmountBase = $this->helperData->convertCurrency($order->getBaseCurrencyCode(), $order->getOrderCurrencyCode(), $multiAddress->getPartialPayNow());
                            $order->setData('total_paid', $order->getTotalPaid() + $multiAddress->getPartialPayNow());
                            $order->setData('base_total_paid', $order->getBaseTotalPaid() + $getAmountBase);
                            $order->setData('total_paid', $order->getTotalPaid() + $multiAddress->getPartialPayNow());
                            $order->setData('base_total_due', $order->getBaseTotalDue() - $getAmountBase);
                        }

                        $modelInstallments = $this->installments->create();
                        $modelInstallments->setPartialPaymentId($modelPartialpayment->getPartialPaymentId())
                            ->setInstallmentAmount($multiAddress->getPartialPayNow())
                            ->setInstallmentPaidDate(date("Y-m-d H:i:s"))
                            ->setInstallmentDueDate(date("Y-m-d H:i:s"))
                            ->setInstallmentStatus($installmentStatus)
                            ->setPaymentMethod($method->getCode())
                            //->setTransactionId($method->getTransactionId())
                            ->setReminderEmail($order->getCustomerEmail());
                        $modelInstallments->save();

                        $remainingAmountArr = [];
                        for ($i = 1; $i < $multiAddress->getPartialMaxInstallment(); $i++) {
                            $remainingAmountArr[$i] = 0;
                        }

                        foreach ($multiAddress->getAllItems() as $quoteItem) {
                            for ($i = 1; $i < $quoteItem->getPartialInstallmentNo(); $i++) {
                                $remainingAmountArr[$i] += $quoteItem->getPartialPayLater() / ($quoteItem->getPartialInstallmentNo() - 1);
                            }
                        }

                        foreach ($remainingAmountArr as $key => $remainingAmount) {
                            $nextInstallmentDate = $this->helperData->getNextInstallmentDate($key);
                            $modelInstallmentsSecond = $this->installments->create();
                            $modelInstallmentsSecond->setPartialPaymentId($modelPartialpayment->getPartialPaymentId())
                                ->setInstallmentAmount($remainingAmount)
                                ->setInstallmentDueDate($nextInstallmentDate)
                                ->setInstallmentStatus(0)
                                ->setPaymentMethod('')
                                ->setTransactionId('')
                                ->setReminderEmail($order->getCustomerEmail());
                            $modelInstallmentsSecond->save();
                        }

                        $installments = $this->partialInstallmentCollection->create()->addFieldToFilter('partial_payment_id', $modelPartialpayment->getId());
                        $partialpayment_installment_grid = "";
                        if ($installments->count() > 0) {
                            $partialpayment_installment_grid .= '<table style="font-size: 13px;color:#010101;width:100%; border-collapse: collapse; margin-bottom:20px" align="center" cellspacing="0" cellpadding="1" border="0">';
                            $partialpayment_installment_grid .= '<tr style="color:#164162;font-size: 16px">';

                            $partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;border-top: none;border-left: none;">Installments <br> Amount</th>';
                            $partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;border-top: none;">Due Date</th>';
                            $partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;border-top: none;">Paid Date</th>';
                            $partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;border-top: none;">Installments <br> Status</th>';
                            $partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;border-top: none;border-right: none;">Payment <br> Method</th>';
                            $partialpayment_installment_grid .= '</tr>';

                            $totalInstallment = $installments->count();
                            $i = 0;
                            foreach ($installments as $installment) {
                                if ($installment->getInstallmentStatus() == 1) {
                                    $status = '<div style="text-align:center;width: 92px !important;padding: 1px 0; color:#FFF;background-color:#86cae4;border-radius:8px;">Processing</div>';
                                    $paidDate = date('Y-m-d', strtotime($installment->getInstallmentPaidDate()));
                                } elseif ($installment->getInstallmentStatus() == 2) {
                                    $status = '<div style="text-align:center;width: 92px !important;padding: 1px 0; color:#FFF;background-color:#f7944b;border-radius:8px;">Paid</div>';
                                    $paidDate = date('Y-m-d', strtotime($installment->getInstallmentPaidDate()));
                                } else {
                                    $status = '<div style="text-align:center;width: 92px !important;padding: 1px 0; color:#FFF;background-color:#434a56;border-radius:8px;">Pending</div>';
                                    $paidDate = 'N / A';
                                }

                                $paymentMethod = $this->scopeConfig->getValue('payment/' . $installment->getPaymentMethod() . '/title');;

                                $i++;
                                if ($i == $totalInstallment) {
                                    $partialpayment_installment_grid .= '<tr>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-left: none;border-bottom: none;" align="center">' . $this->helperData->getFormattedPrice($currencycode, $installment->getInstallmentAmount()) . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . date('Y-m-d', strtotime($installment->getInstallmentDueDate())) . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . $paidDate . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . $status . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;border-right: none;" align="center">' . $paymentMethod . '</td>';
                                    $partialpayment_installment_grid .= '</tr>';
                                } else {
                                    $partialpayment_installment_grid .= '<tr>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-left: none;" align="center">' . $this->helperData->getFormattedPrice($currencycode, $installment->getInstallmentAmount()) . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;" align="center">' . date('Y-m-d', strtotime($installment->getInstallmentDueDate())) . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;" align="center">' . $paidDate . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;" align="center">' . $status . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-right: none;" align="center">' . $paymentMethod . '</td>';
                                    $partialpayment_installment_grid .= '</tr>';
                                }

                            }
                            $partialpayment_installment_grid .= '</table>';
                        }

                        $params = [
                            'customer_name' => $order->getCustomerFirstname() . '  ' . $order->getCustomerLastname(),
                            'order_id' => $order->getIncrementId(),
                            'partialpayment_installment_grid' => $partialpayment_installment_grid
                        ];
                        $storeScope = ScopeInterface::SCOPE_STORE;
                        $sender = $this->scopeConfig->getValue('partialpro/email/schedule/identity', $storeScope);
                        $admintemplateConfigPath = 'partialpro/email/schedule/template';
                        $admintemplate = $this->scopeConfig->getValue($admintemplateConfigPath, $storeScope);
                        $inlineTranslation = $this->inlineTranslation;
                        $inlineTranslation->suspend();
                        $transportBuilder = $this->transportBuilder;
                        if ($this->scopeConfig->getValue('partialpro/email/schedule/copy', $storeScope)) {
                            foreach (explode(',', $this->scopeConfig->getValue('partialpro/email/schedule/copy', $storeScope)) as $bcc) {
                                $transportBuilder->addBcc($bcc, 'Installments Schedule.');
                            }
                        }
                        $transport = $transportBuilder
                            ->setTemplateIdentifier($admintemplate)
                            ->setTemplateOptions(
                                [
                                    'area' => 'frontend',
                                    'store' => Store::DEFAULT_STORE_ID,
                                ]
                            )
                            ->setTemplateVars($params)
                            ->setFrom($sender)
                            ->addTo($order->getCustomerEmail())
                            ->getTransport();
                        $transport->sendMessage();
                    } catch (\Exception $e) {
                        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
                    }
                }
                return $this;
            }
        }
    }
}
