<?php

namespace Meetanshi\Partialpro\Cron;

use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as InstallmentsFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Meetanshi\Partialpro\Model\PartialpaymentFactory;
use Meetanshi\Partialpro\Helper\Data as partialData;
use Magento\Sales\Model\OrderFactory;
use Magento\Paypal\Model\Billing\Agreement;
use Meetanshi\Partialpro\Model\Api\Nvp;
use Meetanshi\Partialpro\Model\AuthorizeCim;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class AutoCapture
{
    protected $partialInstallmentCollection;
    protected $priceHepler;

    protected $inlineTranslation;
    protected $transportBuilder;
    protected $scopeConfig;
    protected $partialpayment;
    protected $partialHelper;
    protected $orderFactory;
    protected $paypalBillingAgreement;
    protected $paypalNvpt;
    protected $authorizeModel;
    protected $encryptor;
    protected $objectManager;

    public function __construct(
        InstallmentsFactory $partialInstallmentCollection,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $configScopeConfigInterface,
        TransportBuilder $transportBuilder,
        PartialpaymentFactory $partialpayment,
        priceHelper $priceHepler,
        partialData $partialData,
        OrderFactory $orderFactory,
        EncryptorInterface $encryptor,
        ObjectManagerInterface $objectManager,
        Agreement $paypalBillingAgreement,
        Nvp $paypalNvp,
        AuthorizeCim $authorizeModel
    )
    {
        $this->partialInstallmentCollection = $partialInstallmentCollection;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $configScopeConfigInterface;
        $this->priceHepler = $priceHepler;
        $this->partialpayment = $partialpayment;
        $this->partialHelper = $partialData;
        $this->orderFactory = $orderFactory;
        $this->paypalBillingAgreement = $paypalBillingAgreement;
        $this->paypalNvp = $paypalNvp;
        $this->authorizeModel = $authorizeModel;
        $this->encryptor = $encryptor;
        $this->objectManager = $objectManager;
    }

    public function execute()
    {
        $installments = $this->partialInstallmentCollection->create();

        $autoCapture = $this->partialHelper->getAutoCapture();

        if ($autoCapture) {

            $fromDate = date('Y-m-d 00:00:00');
            $toDate = date('Y-m-d 23:59:59');

            $installments->addFieldToFilter('installment_due_date', [
                'from' => $fromDate,
                'to' => $toDate
            ]);
            $installments->addfieldtofilter('installment_status', 0);


            if ($installments->count()) {
                foreach ($installments as $installment) {

                    $partialPayment = $this->partialpayment->create()->load($installment->getPartialPaymentId());

                    $partailOrder = $this->partialpayment->create()->load($installment->getPartialPaymentId());
                    $salesOrder = $this->orderFactory->create()->loadByIncrementId($partailOrder->getOrderId());

                    if ($salesOrder->getStatus() != 'close' && $salesOrder->getStatus() != 'canceled') {

                        $payment_method = $salesOrder->getPayment()->getMethodInstance()->getCode();
                        $amount = $this->partialHelper->convertCurrency($salesOrder->getBaseCurrencyCode(), $salesOrder->getOrderCurrencyCode(), $installment->getInstallmentAmount());

                        if ($payment_method == "authorizenet_directpost") {

                            $autoCaptureProfileId = $partailOrder->getAutoCaptureProfileId();
                            $autoCapturePaymentProfileId = $partailOrder->getAutoCapturePaymentProfileId();

                            if ($autoCaptureProfileId && $autoCapturePaymentProfileId) {

                                $response = $this->authorizeModel->autoCaptureAuthorize($autoCaptureProfileId, $autoCapturePaymentProfileId, $amount);

                                if ($response["status"]) {
                                    $this->setInstallmentSuccessData($salesOrder, $partialPayment, $installment, $payment_method);
                                    $this->sendInstallmentSuccessEmail($salesOrder, $partialPayment);
                                } else {

                                    $this->sendInstallmentPaymentFailureMail($salesOrder, $partialPayment, $amount);
                                }
                            }

                        } elseif ($payment_method == "paypal_express" || $payment_method == "paypal_billing_agreement") {

                            $active_agrrement = $this->paypalBillingAgreement->getCollection()
                                ->addFieldToFilter('customer_id', $salesOrder->getCustomerId())
                                ->addFieldToFilter('status', 'active');

                            if ($active_agrrement->count()) {

                                $referenceId = $active_agrrement->getFirstItem()->getReferenceId();
                                $invId = $salesOrder->getIncrementId() . "-" . $installment->getId() . "-" . uniqid();
                                $currenyCode = $salesOrder->getBaseCurrencyCode();

                                $response = $this->paypalNvp->callInstallmentCapture($amount, $referenceId, $invId, $currenyCode);

                                if ($response['ACK'] == "Success") {
                                    $this->setInstallmentSuccessData($salesOrder, $partialPayment, $installment, $payment_method);
                                    $this->sendInstallmentSuccessEmail($salesOrder, $partialPayment);

                                } else {
                                    $this->sendInstallmentPaymentFailureMail($salesOrder, $partialPayment, $amount);
                                }
                            }
                        } elseif ($payment_method == "braintree") {

                            $customerId = $partialPayment->getCustomerId();

                            if (!empty($customerId)) {
                                $card = $this->objectManager->create('\Magento\Vault\Model\PaymentToken')
                                    ->getCollection()
                                    ->addFieldToFilter('customer_id', $customerId)
                                    ->addFieldToFilter('payment_method_code', $payment_method)
                                    ->addFieldToFilter('is_visible', 1)
                                    ->addFieldToFilter('is_active', 1)
                                    ->getFirstItem()
                                    ->getData();

                                if ($card !== false && sizeof($card)) {
                                    $info = [];
                                    $info['method'] = $payment_method;
                                    $info['cc_token'] = $this->encryptor->encrypt($card['gateway_token']);
                                    $info['amount'] = $amount;

                                    $response = $this->objectManager->create('Meetanshi\Partialpro\Model\Payment\Braintree')->payInstallment($salesOrder, $info);

                                    if ($response["success"]) {
                                        $this->setInstallmentSuccessData($salesOrder, $partialPayment, $installment, $payment_method);
                                        $this->sendInstallmentSuccessEmail($salesOrder, $partialPayment);
                                    } else {
                                        $this->sendInstallmentPaymentFailureMail($salesOrder, $partialPayment, $amount);
                                    }
                                }
                            }

                        }
                    }
                }
            }
        }
    }

    protected function setInstallmentSuccessData($order, $partialPayment, $installment, $paymentMethod)
    {
        if ($installment->getId()) {
            $installment->setPaymentMethod($paymentMethod)
                ->setInstallmentStatus(2)
                ->setInstallmentPaidDate(date("Y-m-d H:i:s"))
                ->save();
        }

        $partialPayment->setPaidInstallments($partialPayment->getPaidInstallments() + 1)
            ->setRemainingInstallments($partialPayment->getRemainingInstallments() - 1)
            ->setPaymentStatus(1)
            ->setUpdatedDate(date("Y-m-d H:i:s"))
            ->setPaidAmount($partialPayment->getPaidAmount() + $installment->getInstallmentAmount())
            ->setRemainingAmount($partialPayment->getRemainingAmount() - $installment->getInstallmentAmount())
            ->save();

        $collection = $this->partialInstallmentCollection->create();
        $collection->addFieldToFilter('partial_payment_id', $partialPayment->getId());
        $collection->addFieldToFilter('installment_status', ['in' => [0, 1]]);
        if ($collection->count() == 0) {
            $partialPayment->setPaymentStatus(2)->save();
            $partialPayment->setRemainingAmount(0)->save();
        }

        $getInstallmentPaidAmount = $installment->getInstallmentAmount();
        $getBaseInstallmentPaidAmount = $this->partialHelper->convertCurrency($order->getBaseCurrencyCode(), $order->getOrderCurrencyCode(), $getInstallmentPaidAmount);

        $order->setTotalPaid($order->getTotalPaid() + $getInstallmentPaidAmount)
            ->setBaseTotalPaid($order->getBaseTotalPaid() + $getBaseInstallmentPaidAmount)
            ->setTotalDue($order->getTotalDue() - $getInstallmentPaidAmount)
            ->setBaseTotalDue($order->getBaseTotalDue() - $getBaseInstallmentPaidAmount)
            ->save();
    }

    protected function sendInstallmentSuccessEmail($order, $partialPayment)
    {
        $installments = $this->partialInstallmentCollection->create()->addFieldToFilter('partial_payment_id', $partialPayment->getId());
        $currencycode = $partialPayment->getCurrencyCode();
        $partialpayment_installment_grid = "";
        if ($installments->count() > 0) {

            $partialpayment_installment_grid .= '<table style="font-size: 14px;color:#010101;width:100%; border-collapse: collapse; margin-bottom:20px" align="center" cellspacing="0" cellpadding="1" border="0">';
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
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-left: none;border-bottom: none;" align="center">' . $this->partialHelper->getFormattedPrice($currencycode, $installment->getInstallmentAmount()) . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . date('Y-m-d', strtotime($installment->getInstallmentDueDate())) . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . $paidDate . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . $status . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;border-right: none;" align="center">' . $paymentMethod . '</td>';
                    $partialpayment_installment_grid .= '</tr>';
                } else {
                    $partialpayment_installment_grid .= '<tr>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-left: none;" align="center">' . $this->partialHelper->getFormattedPrice($currencycode, $installment->getInstallmentAmount()) . '</td>';
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
        $sender = $this->scopeConfig->getValue('partialpro/email/payment/identity', $storeScope);
        $admintemplateConfigPath = 'partialpro/email/payment/template';
        $admintemplate = $this->scopeConfig->getValue($admintemplateConfigPath, $storeScope);
        $inlineTranslation = $this->inlineTranslation;
        $inlineTranslation->suspend();
        $transportBuilder = $this->transportBuilder;
        if ($this->scopeConfig->getValue('partialpro/email/payment/copy', $storeScope)) {
            foreach (explode(',', $this->scopeConfig->getValue('partialpro/email/payment/copy', $storeScope)) as $bcc) {
                $transportBuilder->addBcc($bcc, 'Installments Payment.');
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
    }


    protected function sendInstallmentPaymentFailureMail($order, $partialPayment, $amount)
    {
        $installments = $this->partialInstallmentCollection->create()->addFieldToFilter('partial_payment_id', $partialPayment->getId());
        $currencycode = $partialPayment->getCurrencyCode();
        $partialpayment_installment_grid = "";
        if ($installments->count() > 0) {
            $partialpayment_installment_grid .= '<table style="font-size: 14px;color:#010101;width:100%; border-collapse: collapse; margin-bottom:20px" align="center" cellspacing="0" cellpadding="1" border="0">';
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
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-left: none;border-bottom: none;" align="center">' . $this->partialHelper->getFormattedPrice($currencycode, $installment->getInstallmentAmount()) . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . date('Y-m-d', strtotime($installment->getInstallmentDueDate())) . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . $paidDate . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . $status . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;border-right: none;" align="center">' . $paymentMethod . '</td>';
                    $partialpayment_installment_grid .= '</tr>';
                } else {
                    $partialpayment_installment_grid .= '<tr>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-left: none;" align="center">' . $this->partialHelper->getFormattedPrice($currencycode, $installment->getInstallmentAmount()) . '</td>';
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
            'partialpayment_installment_grid' => $partialpayment_installment_grid,
            'amount' => $this->priceHepler->currency(number_format($amount, 2), true, false)
        ];
        $storeScope = ScopeInterface::SCOPE_STORE;
        $sender = $this->scopeConfig->getValue('partialpro/email/failure/identity', $storeScope);
        $admintemplateConfigPath = 'partialpro/email/failure/template';
        $admintemplate = $this->scopeConfig->getValue($admintemplateConfigPath, $storeScope);
        $inlineTranslation = $this->inlineTranslation;
        $inlineTranslation->suspend();
        $transportBuilder = $this->transportBuilder;
        if ($this->scopeConfig->getValue('partialpro/email/failure/copy', $storeScope)) {
            foreach (explode(',', $this->scopeConfig->getValue('partialpro/email/failure/copy', $storeScope)) as $bcc) {
                $transportBuilder->addBcc($bcc, 'Installments Payment Failure.');
            }
        }

        try {
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
}
