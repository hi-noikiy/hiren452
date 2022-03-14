<?php

namespace Meetanshi\Partialpro\Controller\Ravepayment;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Framework\App\Action;
use Meetanshi\Partialpro\Model\Partialpayment;
use Meetanshi\Partialpro\Model\Installments;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as PartialInstallmentCollection;
use Meetanshi\Partialpro\Model\ResourceModel\Partialpayment\CollectionFactory as PartialPaymentCollection;
use Magento\Framework\Pricing\Helper\Data as priceHelper;

use Magento\Sales\Model\OrderFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Helper\Data as CheckoutHelper;
use Magento\Sales\Model\OrderNotifier;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Framework\App\Request\Http;
use Magento\Sales\Model\Order\Payment\Transaction\Builder;
use Meetanshi\Partialpro\Helper\Data as partialHelper;

class Accept extends Action\Action
{

    protected $partialPaymentOrder;
    protected $partialInstallment;
    protected $transportBuilder;
    protected $inlineTranslation;
    protected $scopeConfig;
    protected $partialInstallmentCollection;
    protected $partialPaymentCollection;
    protected $priceHepler;
    protected $checkoutSession;
    protected $orderFactory;
    protected $orderSender;
    protected $request;
    protected $transactionBuilder;
    public $partialHelper;

    public function __construct(
        Action\Context $context,
        Partialpayment $partialPaymentOrder,
        Installments $partialInstallment,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $configScopeConfigInterface,
        TransportBuilder $transportBuilder,
        PartialInstallmentCollection $partialInstallmentCollection,
        priceHelper $priceHepler,
        OrderFactory $orderFactory,
        CheckoutSession $checkoutSession,
        CheckoutHelper $checkoutData,
        OrderNotifier $orderSender,
        InvoiceSender $invoiceSender,
        Http $request,
        Builder $transactionBuilder,
        PartialPaymentCollection $partialPaymentCollection,
        partialHelper $partialHelper,
        $params = []
    )
    {
        $this->partialPaymentOrder = $partialPaymentOrder;
        $this->partialInstallment = $partialInstallment;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $configScopeConfigInterface;
        $this->partialInstallmentCollection = $partialInstallmentCollection;
        $this->partialPaymentCollection = $partialPaymentCollection;
        $this->priceHepler = $priceHepler;
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->orderSender = $orderSender;
        $this->request = $request;
        $this->transactionBuilder = $transactionBuilder;
        $this->partialHelper = $partialHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $getdata = $this->request->getParams();
        $allarrparams = json_decode($getdata['resp'], true);

        try {

            if ($allarrparams['success']) {

                $params = $allarrparams['tx'];

                if (strpos($params['txRef'], '-') !== false) {

                    //Partial Installment code

                    if ($params['vbvrespcode'] == '00') {

                        $installmentArrId = explode("-", $params['txRef']);
                        $sizeofArr = sizeof($installmentArrId);

                        if ($sizeofArr > 1) {

                            $order = $this->orderFactory->create()->loadByIncrementId($installmentArrId[0]);
                            $installment = $this->partialInstallment->load($installmentArrId[1]);
                            $partialPayment = $this->partialPaymentOrder->load($installment->getPartialPaymentId());

                            if ($order->getId()) {

                                $incrementId = $order->getIncrementId();
                                $incrementIdStr = "#" . $incrementId;

                                $i = 0;
                                foreach ($installmentArrId as $installmentId) {
                                    if ($i == 0) {
                                        $i++;
                                        continue;
                                    }
                                    $installment = $this->partialInstallment->load($installmentId);
                                    $this->setInstallmentSuccessData($order, $partialPayment, $installment, $params['id']);

                                    try {
                                        $orderMain = $this->orderFactory->create()->loadByIncrementId($installmentArrId[0]);

                                        $getInstallmentPaidAmount = $installment->getInstallmentAmount();
                                        $getBaseInstallmentPaidAmount = $this->partialHelper->convertCurrency($order->getBaseCurrencyCode(), $order->getOrderCurrencyCode(), $getInstallmentPaidAmount);

                                        $orderMain->setTotalPaid($orderMain->getTotalPaid() + $getInstallmentPaidAmount)
                                            ->setBaseTotalPaid($orderMain->getBaseTotalPaid() + $getBaseInstallmentPaidAmount)
                                            ->setTotalDue($orderMain->getTotalDue() - $getInstallmentPaidAmount)
                                            ->setBaseTotalDue($orderMain->getBaseTotalDue() - $getBaseInstallmentPaidAmount)
                                            ->save();

                                    } catch (\Exception $e) {
                                        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());
                                    }
                                }
                                $this->sendInstallmentSuccessEmail($order, $partialPayment);

                                $this->messageManager->addSuccessMessage(__('Installment of order %1 is paid successfully.', $incrementIdStr));
                                $this->_redirect('partialpayment/account/view', ['profile' => $installment->getPartialPaymentId(), '_current' => false]);

                            } else {
                                $this->messageManager->addErrorMessage(__('Something went wrong.'));
                                $this->_redirect('partialpayment/account/view', ['profile' => $installment->getPartialPaymentId(), '_current' => false]);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }
    }

    protected function setInstallmentSuccessData($order, $partialPayment, $installment, $transactionid)
    {
        if ($installment->getId()) {
            $installment->setPaymentMethod('orangeivory')
                ->setInstallmentStatus(2)
                ->setInstallmentPaidDate(date("Y-m-d H:i:s"))
                ->setTransactionId($transactionid)
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

    }

    protected function sendInstallmentSuccessEmail($order, $partialPayment)
    {
        try {
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
                        $partialpayment_installment_grid .= '<td style ="border: 1px solid;border-bottom:none;padding: 10px 5px;border-left:none;" align="center">' . $this->partialHelper->getFormattedPrice($currencycode, $installment->getInstallmentAmount()) . '</td>';
                        $partialpayment_installment_grid .= '<td style ="border: 1px solid;border-bottom:none;padding: 10px 5px;" align="center">' . date('Y-m-d', strtotime($installment->getInstallmentDueDate())) . '</td>';
                        $partialpayment_installment_grid .= '<td style ="border: 1px solid;border-bottom:none;padding: 10px 5px;" align="center">' . $paidDate . '</td>';
                        $partialpayment_installment_grid .= '<td style ="border: 1px solid;border-bottom:none;padding: 10px 5px;" align="center">' . $status . '</td>';
                        $partialpayment_installment_grid .= '<td style ="border: 1px solid;border-bottom:none;padding: 10px 5px;border-right:none " align="center">' . $paymentMethod . '</td>';
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
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }
    }
}