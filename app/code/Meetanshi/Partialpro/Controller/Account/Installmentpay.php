<?php

namespace Meetanshi\Partialpro\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Meetanshi\Partialpro\Model\Installments;
use Meetanshi\Partialpro\Model\Partialpayment;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as PartialInstallmentCollection;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Payment\Helper\Data as paymentHelper;
use Meetanshi\Partialpro\Model\InstallmentPaymentHandler;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Paypal\Model\Billing\Agreement;
use Meetanshi\Partialpro\Model\Api\Nvp;
use Meetanshi\Partialpro\Helper\Mtn as mtnHelper;

class Installmentpay extends Action
{
    protected $resultPageFactory;
    protected $session;
    protected $installments;
    protected $partialpayment;
    protected $priceHepler;

    protected $inlineTranslation;
    protected $transportBuilder;
    protected $scopeConfig;
    protected $soapHandle;
    protected $partialInstallmentCollection;
    protected $paymentHelper;
    protected $installmentPaymentHandler;
    protected $dataHelper;
    protected $paypalBillingAgreement;
    protected $paypalNvp;
    protected $order;
    protected $mtnHelper;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        Installments $installments,
        Partialpayment $partialpayment,
        Order $order,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $configScopeConfigInterface,
        TransportBuilder $transportBuilder,
        PartialInstallmentCollection $partialInstallmentCollection,
        priceHelper $priceHepler,
        paymentHelper $paymentHelper,
        Data $dataHelper,
        Nvp $paypalNvp,
        Agreement $paypalBillingAgreement,
        InstallmentPaymentHandler $installmentPaymentHandler,
        mtnHelper $mtnHelper
    )
    {

        $this->session = $customerSession;
        $this->installments = $installments;
        $this->partialpayment = $partialpayment;
        $this->order = $order;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $configScopeConfigInterface;
        $this->partialInstallmentCollection = $partialInstallmentCollection;
        $this->priceHepler = $priceHepler;
        $this->paymentHelper = $paymentHelper;
        $this->dataHelper = $dataHelper;
        $this->paypalBillingAgreement = $paypalBillingAgreement;
        $this->paypalNvp = $paypalNvp;
        $this->installmentPaymentHandler = $installmentPaymentHandler;
        $this->mtnHelper = $mtnHelper;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if (!$this->session->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        } else {
            if ($data) {
                try {
                    if (isset($data['partial_payment_id']) && isset($data['pay']) && isset($data['order_id']) && isset($data['payment_method'])) {
                        $partialPayment = $this->partialpayment->load($data['partial_payment_id']);
                        $order = $this->order->loadByIncrementId($data['order_id']);
                        $payment = $data;
                        $payment['method'] = $data['payment_method'];
                        if ($partialPayment->getId() && sizeof($data['pay']) > 0 && $order->getId()) {

                            $paymentMethod = $this->paymentHelper->getMethodInstance($data['payment_method']);
                            $amount = 0;

                            if ($paymentMethod->isOffline()) {
                                foreach ($data['pay'] as $installmentId) {
                                    $installment = $this->installments->load($installmentId);
                                    $this->setInstallmentSuccessData($order, $partialPayment, $installment, $data['payment_method']);
                                }
                                $this->sendInstallmentSuccessEmail($order, $partialPayment);
                                $this->messageManager->addSuccessMessage(
                                    __('Installment paid Successfully.')
                                );

                                $this->_redirect($this->_redirect->getRefererUrl());
                                return;

                            } else {

                                $payment['installments'] = implode("-", $data['pay']);
                                foreach ($data['pay'] as $installmentIds) {
                                    $installmentData = $this->installments->load($installmentIds);
                                    if ($installmentData->getInstallmentStatus() != 2) {
                                        $amount += round($installmentData->getInstallmentAmount(), 2);
                                        $partialPaymentId = $installmentData->getPartialPaymentId();
                                    }
                                }

                                $payment['amount'] = $amount;

                                if ($data['payment_method'] == 'authorizenet_directpost') {

                                    $response = $this->processPayment($payment, $partialPaymentId);

                                    if ($response['success'] == true) {
                                        foreach ($data['pay'] as $installmentId) {
                                            $installment = $this->installments->load($installmentId);
                                            $this->setInstallmentSuccessData($order, $partialPayment, $installment, $data['payment_method']);
                                        }
                                        $this->sendInstallmentSuccessEmail($order, $partialPayment);
                                        $this->messageManager->addSuccessMessage(
                                            __('Installment paid Successfully.')
                                        );
                                        $this->_redirect($this->_redirect->getRefererUrl());
                                        return;

                                    } else {
                                        if (isset($response['message']) && $response['message'] != '') {
                                            $this->messageManager->addErrorMessage(__($response['message']));
                                        } else {
                                            $this->messageManager->addErrorMessage(__('Installment payment failed.'));
                                        }
                                        $this->_redirect($this->_redirect->getRefererUrl());
                                        return;
                                    }
                                } else if ($data['payment_method'] == 'paypal_express') {
                                    $response = $this->processPayment($payment, $partialPaymentId);

                                    if ($response['success'] == true) {
                                        return $this->resultRedirectFactory->create()->setUrl($response['redirect_url']);

                                    } else {
                                        if (isset($response['message']) && $response['message'] != '') {
                                            $this->messageManager->addErrorMessage(__($response['message']));
                                        } else {
                                            $this->messageManager->addErrorMessage(__('Installment payment failed.'));
                                        }
                                        $this->_redirect($this->_redirect->getRefererUrl());
                                        return;
                                    }

                                } else if ($data['payment_method'] == 'braintree') {

                                    $response = $this->processPayment($payment, $partialPaymentId);

                                    if ($response['success'] == true) {
                                        foreach ($data['pay'] as $installmentId) {
                                            $installment = $this->installments->load($installmentId);
                                            $this->setInstallmentSuccessData($order, $partialPayment, $installment, $data['payment_method']);
                                        }
                                        $this->sendInstallmentSuccessEmail($order, $partialPayment);
                                        $this->messageManager->addSuccessMessage(
                                            __('Installment paid Successfully.')
                                        );
                                        $this->_redirect($this->_redirect->getRefererUrl());
                                        return;

                                    } else {
                                        if (isset($response['message']) && $response['message'] != '') {
                                            $this->messageManager->addErrorMessage(__($response['message']));
                                        } else {
                                            $this->messageManager->addErrorMessage(__('Installment payment failed.'));
                                        }
                                        $this->_redirect($this->_redirect->getRefererUrl());
                                        return;
                                    }
                                } else if ($data['payment_method'] == 'sagepay') {


                                    $partialpaymentOrder = $this->partialpayment->load($partialPaymentId);
                                    $order = $this->order->loadByIncrementId($partialpaymentOrder->getOrderId());
                                    $response = $this->installmentPaymentHandler->payInstallments($order, $payment);

                                    if ($response['success'] == true) {
                                        foreach ($data['pay'] as $installmentId) {
                                            $installment = $this->installments->load($installmentId);
                                            $this->setInstallmentSuccessData($order, $partialPayment, $installment, $data['payment_method']);
                                        }
                                        $this->sendInstallmentSuccessEmail($order, $partialPayment);
                                        $this->messageManager->addSuccessMessage(
                                            __('Installment paid Successfully.')
                                        );
                                        $this->_redirect($this->_redirect->getRefererUrl());
                                        return;

                                    } else {
                                        if (isset($response['message']) && $response['message'] != '') {
                                            $this->messageManager->addErrorMessage(__($response['message']));
                                        } else {
                                            $this->messageManager->addErrorMessage(__('Installment payment failed.'));
                                        }
                                        $this->_redirect($this->_redirect->getRefererUrl());
                                        return;
                                    }
                                } else if ($data['payment_method'] == 'paypal_billing_agreement') {

                                    $aggrementId = $data['payment']['ba_agreement_id'];
                                    if ($aggrementId > 0) {

                                        $partialpaymentOrder = $this->partialpayment->load($partialPaymentId);
                                        $salesOrder = $this->order->loadByIncrementId($partialpaymentOrder->getOrderId());

                                        $active_agrrement = $this->paypalBillingAgreement->load($aggrementId);

                                        if ($active_agrrement->getId()) {

                                            $referenceId = $active_agrrement->getReferenceId();
                                            $invId = $salesOrder->getIncrementId() . "-" . $data['installments'] . "-" . uniqid();
                                            $currenyCode = $salesOrder->getBaseCurrencyCode();

                                            $basePartialPaymentFee = $this->dataHelper->convertCurrency($order->getBaseCurrencyCode(), $order->getOrderCurrencyCode(), $amount);
                                            $amount = $basePartialPaymentFee;

                                            $response = $this->paypalNvp->callInstallmentCapture($amount, $referenceId, $invId, $currenyCode);

                                            if ($response['ACK'] == "Success") {

                                                foreach ($data['pay'] as $installmentId) {
                                                    $installment = $this->installments->load($installmentId);
                                                    $this->setInstallmentSuccessData($order, $partialPayment, $installment, $data['payment_method']);
                                                }
                                                $this->sendInstallmentSuccessEmail($order, $partialPayment);
                                                $this->messageManager->addSuccessMessage(
                                                    __('Installment paid Successfully.')
                                                );
                                                $this->_redirect($this->_redirect->getRefererUrl());
                                                return;

                                            }
                                        }
                                    } else {

                                        $this->messageManager->addErrorMessage(
                                            __('Please Select Billing Agreement.')
                                        );
                                        $this->_redirect($this->_redirect->getRefererUrl());
                                        return;
                                    }
                                } else if ($data['payment_method'] == 'ravepayment') {

                                    $inst_id = $payment['installments'];
                                    $params = ['inst_id' => $inst_id];

                                    $resultRedirect = $this->resultRedirectFactory->create();
                                    $resultRedirect->setPath('partialpayment/ravepayment/redirect/', $params);
                                    return $resultRedirect;
                                }else if ($data['payment_method'] == 'orangeivory') {

                                    $inst_id = $payment['installments'];
                                    $params = ['inst_id' => $inst_id];

                                    $resultRedirect = $this->resultRedirectFactory->create();
                                    $resultRedirect->setPath('partialpayment/orangeivory/redirect/', $params);
                                    return $resultRedirect;
                                } else if ($data['payment_method'] == 'tagpay') {

                                    $inst_id = $payment['installments'];
                                    $params = ['inst_id' => $inst_id];

                                    $resultRedirect = $this->resultRedirectFactory->create();
                                    $resultRedirect->setPath('partialpayment/tagpay/redirect/', $params);
                                    return $resultRedirect;
                                } else if ($data['payment_method'] == 'mtn') {

                                    $mtnNumber = $data['mtn_number'];
                                    $referanceNumber = $data['order_id'] . '-' . $data['installments'];
                                    $amount = $data['amount'];
                                    $mtnCode = $this->mtnHelper->getBillMapCode();
                                    $mtnPassword = $this->mtnHelper->getBillMapPass();
                                    $apiUrl = $this->mtnHelper->getGatewayUrl();

                                    $curl = curl_init();
                                    curl_setopt_array($curl, array(
                                        CURLOPT_URL => "$apiUrl",
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => "",
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 360,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_SSL_VERIFYPEER => 0,
                                        CURLOPT_CUSTOMREQUEST => "POST",
                                        CURLOPT_POSTFIELDS => "Code=$mtnCode&Password=$mtnPassword&MSISDN=$mtnNumber&Reference=$referanceNumber&Amount=$amount&MetaData=$referanceNumber",
                                        CURLOPT_HTTPHEADER => array(
                                            "cache-control: no-cache",
                                            "content-type: application/x-www-form-urlencoded"
                                        ),
                                    ));
                                    set_time_limit(360);
                                    $xml = curl_exec($curl);

                                    curl_close($curl);

                                    $responce = new \SimpleXMLElement($xml);
                                    $responce = json_decode(json_encode((array)$responce), true);

                                    if ($responce['ResponseCode'] != '1000') {
                                        $this->messageManager->addErrorMessage(__($responce['ResponseMessage']));
                                        $this->_redirect($this->_redirect->getRefererUrl());
                                        return;
                                    }

                                    foreach ($data['pay'] as $installmentIds) {
                                        $installmentData = $this->installments->load($installmentIds);
                                        if ($installmentData->getInstallmentStatus() != 2) {
                                            $installmentData->setTransactionId($responce['BillMapTransactionId'])
                                                ->setInstallmentStatus(1)
                                                ->setPaymentMethod('mtn')
                                                ->setInstallmentPaidDate(date("Y-m-d H:i:s"))
                                                ->save();
                                        }
                                    }

                                }
                            }
                            $this->messageManager->addSuccessMessage(
                                __('Installment paid Successfully.')
                            );
                            $this->_redirect($this->_redirect->getRefererUrl());
                            return;
                        }
                    }
                } catch
                (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __($e->getMessage())
                    );
                    $this->_redirect($this->_redirect->getRefererUrl());
                }
            }
        }
        $this->messageManager->addErrorMessage(
            __('Something went wrong,  please try again after some time.')
        );
        $this->_redirect($this->_redirect->getRefererUrl());
    }

    protected
    function processPayment($payment, $partialId)
    {
        $partialpaymentOrder = $this->partialpayment->load($partialId);
        $order = $this->order->loadByIncrementId($partialpaymentOrder->getOrderId());

        $basePartialPaymentFee = $this->dataHelper->convertCurrency($order->getBaseCurrencyCode(), $order->getOrderCurrencyCode(), $payment['amount']);
        $payment['amount'] = $basePartialPaymentFee;

        $response = $this->installmentPaymentHandler->payInstallments($order, $payment);
        return $response;
    }

    protected function setInstallmentSuccessData($order, $partialPayment, $installment, $paymentMethod)
    {
        if ($installment->getId()) {
            $installment->setPaymentMethod($paymentMethod)
                ->setInstallmentStatus(1)
                ->setInstallmentPaidDate(date("Y-m-d H:i:s"))
                ->save();

            if ($paymentMethod == 'authorizenet_directpost' || $paymentMethod == 'braintree' || $paymentMethod == 'paypal_billing_agreement' || $paymentMethod == 'sagepay') {
                $installment->setInstallmentStatus(2)->save();
            }
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


        if ($installment->getInstallmentStatus() == 2) {

            $getInstallmentPaidAmount = $installment->getInstallmentAmount();
            $getBaseInstallmentPaidAmount = $this->dataHelper->convertCurrency($order->getBaseCurrencyCode(), $order->getOrderCurrencyCode(), $getInstallmentPaidAmount);

            $order->setTotalPaid($order->getTotalPaid() + $getInstallmentPaidAmount)
                ->setBaseTotalPaid($order->getBaseTotalPaid() + $getBaseInstallmentPaidAmount)
                ->setTotalDue($order->getTotalDue() - $getInstallmentPaidAmount)
                ->setBaseTotalDue($order->getBaseTotalDue() - $getBaseInstallmentPaidAmount)
                ->save();

        }

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
                    $status = '<div style="text-align:center;width: 92px !important;padding: 1px 0; color:#FFF;background-color:#434a56; border-radius:8px;">Pending</div>';
                    $paidDate = 'N / A';
                }

                $paymentMethod = $this->scopeConfig->getValue('payment/' . $installment->getPaymentMethod() . '/title');;

                $i++;
                if ($i == $totalInstallment) {

                    $partialpayment_installment_grid .= '<tr>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;border-bottom:none;padding: 10px 5px;border-left:none;" align="center">' . $this->dataHelper->getFormattedPrice($currencycode, $installment->getInstallmentAmount()) . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;border-bottom:none;padding: 10px 5px;" align="center">' . date('Y-m-d', strtotime($installment->getInstallmentDueDate())) . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;border-bottom:none;padding: 10px 5px;" align="center">' . $paidDate . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;border-bottom:none;padding: 10px 5px;" align="center">' . $status . '</td>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;border-bottom:none;padding: 10px 5px;border-right:none " align="center">' . $paymentMethod . '</td>';
                    $partialpayment_installment_grid .= '</tr>';

                } else {
                    $partialpayment_installment_grid .= '<tr>';
                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-left: none;" align="center">' . $this->dataHelper->getFormattedPrice($currencycode, $installment->getInstallmentAmount()) . '</td>';
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
}
