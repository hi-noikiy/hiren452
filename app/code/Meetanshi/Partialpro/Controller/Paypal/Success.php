<?php

namespace Meetanshi\Partialpro\Controller\Paypal;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\Http;
use Meetanshi\Partialpro\Model\Api\Nvp;
use Meetanshi\Partialpro\Model\Partialpayment;
use Magento\Sales\Model\OrderFactory;
use Meetanshi\Partialpro\Model\Installments;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as PartialInstallmentCollection;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Meetanshi\Partialpro\Helper\Data as partialHelper;

class Success extends Action\Action
{

    protected $resultPageFactory;
    protected $partialpaymentCron;

    protected $inlineTranslation;
    protected $transportBuilder;
    protected $scopeConfig;
    protected $soapHandle;
    protected $partialInstallmentCollection;
    protected $request;
    protected $partialPaymentOrder;
    protected $orderFactory;
    protected $PaypalNvp;
    protected $partialInstallment;
    protected $priceHepler;
    public $partialHelper;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Http $request,
        Nvp $PaypalNvp,
        Partialpayment $partialPaymentOrder,
        OrderFactory $orderFactory,
        Installments $partialInstallment,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $configScopeConfigInterface,
        TransportBuilder $transportBuilder,
        PartialInstallmentCollection $partialInstallmentCollection,
        priceHelper $priceHepler,
        partialHelper $partialHelper
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->PaypalNvp = $PaypalNvp;
        $this->partialPaymentOrder = $partialPaymentOrder;
        $this->orderFactory = $orderFactory->create();
        $this->partialInstallment = $partialInstallment;
        $this->request = $request;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $configScopeConfigInterface;
        $this->partialInstallmentCollection = $partialInstallmentCollection;
        $this->priceHepler = $priceHepler;
        $this->partialHelper = $partialHelper;
        parent::__construct($context);
    }


    public function execute()
    {
        $token = $this->request->getParam('token');
        $payerID = $this->request->getParam('PayerID');

        $response = $this->PaypalNvp->callInstallmentGetExpressCheckout($token);

        $invNum = $response['PAYMENTREQUEST_0_INVNUM'];
        $invArray = explode("-", $invNum);

        $installment = $this->partialInstallment->load($invArray[1]);
        $partialPayment = $this->partialPaymentOrder->load($installment->getPartialPaymentId());
        $order = $this->orderFactory->loadByIncrementId($partialPayment->getOrderId());

        $amount = $response['PAYMENTREQUEST_0_AMT'];

        $response = $this->PaypalNvp->callInstallmentDoExpressCheckoutPayment($token, $payerID, $invNum, $amount, $order);

        if ($response['ACK'] == 'Success') {
            $incrementId = $order->getIncrementId();
            $incrementIdStr = "<strong>#" . $incrementId . "</strong>";

            $i = 0;
            foreach ($invArray as $installmentId) {
                if ($i == 0) {
                    $i++;
                    continue;
                }
                $installment = $this->partialInstallment->load($installmentId);
                $this->setInstallmentSuccessData($order, $partialPayment, $installment, $response['TRANSACTIONID']);
            }

            $this->sendInstallmentSuccessEmail($order, $partialPayment);

            $this->messageManager->addSuccess(__('Installment of order %1 is paid successfully.', $incrementIdStr));
            $this->_redirect('partialpayment/account/view', ['profile' => $installment->getPartialPaymentId(), '_current' => true]);
        } else {
            $this->messageManager->addError(__('Payment Transaction request rejected by paypal.'));
            $this->_redirect('partialpayment/account/view', ['profile' => $installment->getPartialPaymentId(), '_current' => true]);
        }
        return $this->resultPageFactory->create();
    }

    protected function setInstallmentSuccessData($order, $partialPayment, $installment, $transactionid)
    {
        if ($installment->getId()) {
            $installment->setPaymentMethod('paypal_express')
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
