<?php

namespace Meetanshi\Partialpro\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Meetanshi\Partialpro\Model\Installments;
use Meetanshi\Partialpro\Model\Partialpayment;
use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as InstallmentsFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Sales\Model\Order;
use Meetanshi\Partialpro\Helper\Data as partialHelper;


class Approve extends Action
{
    protected $resultPageFactory;
    protected $installments;
    protected $partialpayment;
    protected $partialInstallmentCollection;
    protected $priceHepler;

    protected $inlineTranslation;
    protected $transportBuilder;
    protected $scopeConfig;
    protected $order;
    public $partialHelper;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Installments $installments,
        Partialpayment $partialpayment,
        InstallmentsFactory $partialInstallmentCollection,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $configScopeConfigInterface,
        TransportBuilder $transportBuilder,
        Order $order,
        partialHelper $partialHelper,
        priceHelper $priceHepler
    )
    {
        $this->installments = $installments;
        $this->partialpayment = $partialpayment;
        $this->partialInstallmentCollection = $partialInstallmentCollection;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $configScopeConfigInterface;
        $this->priceHepler = $priceHepler;
        $this->partialHelper = $partialHelper;
        $this->order = $order;
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if ($data) {
            try {
                if (isset($data['installment_id']) && isset($data['partial_payment_id'])) {
                    $partialPayment = $this->partialpayment->load($data['partial_payment_id']);
                    $currencycode = $partialPayment->getCurrencyCode();

                    if ($partialPayment->getId()) {
                        $installment = $this->installments->load($data['installment_id']);
                        if ($installment->getId()) {
                            $installment->setInstallmentStatus(2)->save();
                            $order = $this->order->loadByIncrementId($partialPayment->getOrderId());

                            $getInstallmentPaidAmount = $installment->getInstallmentAmount();
                            $getBaseInstallmentPaidAmount = $this->partialHelper->convertCurrency($order->getBaseCurrencyCode(), $order->getOrderCurrencyCode(), $getInstallmentPaidAmount);

                            $order->setTotalPaid($order->getTotalPaid() + $getInstallmentPaidAmount)
                                ->setBaseTotalPaid($order->getBaseTotalPaid() + $getBaseInstallmentPaidAmount)
                                ->setTotalDue($order->getTotalDue() - $getInstallmentPaidAmount)
                                ->setBaseTotalDue($order->getBaseTotalDue() - $getBaseInstallmentPaidAmount)
                                ->save();
                        }

                        $collection = $this->partialInstallmentCollection->create();
                        $collection->addFieldToFilter('partial_payment_id', $partialPayment->getId());
                        $collection->addFieldToFilter('installment_status', ['in' => [0, 1]]);
                        if ($collection->count() == 0) {
                            $partialPayment->setPaymentStatus(2)->save();
                            $partialPayment->setRemainingAmount(0)->save();
                        }

                        $installments = $this->partialInstallmentCollection->create()->addFieldToFilter('partial_payment_id', $partialPayment->getId());
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
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-left: none;border-bottom: none;" align="center">' . $this->partialHelper->getFormattedPrice($currencycode,$installment->getInstallmentAmount()) . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . date('Y-m-d', strtotime($installment->getInstallmentDueDate())) . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . $paidDate . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;" align="center">' . $status . '</td>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: none;border-right: none;" align="center">' . $paymentMethod . '</td>';
                                    $partialpayment_installment_grid .= '</tr>';
                                } else {
                                    $partialpayment_installment_grid .= '<tr>';
                                    $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-left: none;" align="center">' . $this->partialHelper->getFormattedPrice($currencycode,$installment->getInstallmentAmount()) . '</td>';
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
                            'customer_name' => $partialPayment->getCustomerName(),
                            'order_id' => $partialPayment->getOrderId(),
                            'partialpayment_installment_grid' => $partialpayment_installment_grid
                        ];
                        $storeScope = ScopeInterface::SCOPE_STORE;

                        $sender = $this->scopeConfig->getValue('partialpro/email/approve/identity', $storeScope);
                        $admintemplateConfigPath = 'partialpro/email/approve/template';
                        $admintemplate = $this->scopeConfig->getValue($admintemplateConfigPath, $storeScope);

                        $inlineTranslation = $this->inlineTranslation;
                        $inlineTranslation->suspend();
                        $transportBuilder = $this->transportBuilder;
                        if ($this->scopeConfig->getValue('partialpro/email/approve/copy', $storeScope)) {
                            foreach (explode(',', $this->scopeConfig->getValue('partialpro/email/approve/copy', $storeScope)) as $bcc) {
                                $transportBuilder->addBcc($bcc, 'Installment payment approved successfully.');
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
                            ->addTo($partialPayment->getCustomerEmail())
                            ->getTransport();
                        $transport->sendMessage();

                        $this->messageManager->addSuccessMessage(
                            __('Installment payment approved successfully.')
                        );
                        $this->_redirect($this->_redirect->getRefererUrl());
                        return;
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __($e->getMessage())
                );
                $this->_redirect($this->_redirect->getRefererUrl());
            }
        }
        $this->messageManager->addErrorMessage(
            __('Something went wrong,  please try again after some time.')
        );
        $this->_redirect($this->_redirect->getRefererUrl());
    }

    protected function _isAllowed()
    {
        return true;
    }
}
