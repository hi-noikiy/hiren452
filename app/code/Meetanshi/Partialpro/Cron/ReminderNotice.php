<?php

namespace Meetanshi\Partialpro\Cron;

use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as InstallmentsFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Meetanshi\Partialpro\Model\Partialpayment;
use Meetanshi\Partialpro\Helper\Data as partialData;

class ReminderNotice
{
    protected $partialInstallmentCollection;
    protected $priceHepler;

    protected $inlineTranslation;
    protected $transportBuilder;
    protected $scopeConfig;
    protected $partialpayment;
    protected $partialHelper;

    public function __construct(
        InstallmentsFactory $partialInstallmentCollection,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $configScopeConfigInterface,
        TransportBuilder $transportBuilder,
        Partialpayment $partialpayment,
        priceHelper $priceHepler,
        partialData $partialData
    )
    {

        $this->partialInstallmentCollection = $partialInstallmentCollection;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $configScopeConfigInterface;
        $this->priceHepler = $priceHepler;
        $this->partialpayment = $partialpayment;
        $this->partialHelper = $partialData;
    }

    public function execute()
    {
        $installments = $this->partialInstallmentCollection->create();

        $autoCapture = $this->partialHelper->getAutoCapture();
        if ($autoCapture) {
            $dayDiff = $this->partialHelper->getAutoCaptureReminderDays();
            $templateKey = 'autocapture';
        } else {
            $dayDiff = $this->partialHelper->getReminderDays();
            $templateKey = 'reminder';
        }

        $time = time();
        $lastTime = $time + (60 * 60 * 24 * $dayDiff);
        $fromDate = date('Y-m-d 00:00:00', $lastTime);
        $toDate = date('Y-m-d 23:59:59', $lastTime);


        $installments->addFieldToFilter('installment_due_date', [
            'from' => $fromDate,
            'to' => $toDate
        ]);
        $installments->addfieldtofilter('installment_status', 0);


        if ($installments->count() > 0) {
            foreach ($installments as $installment) {
                $partialPayment = $this->partialpayment->load($installment->getPartialPaymentId());
                $currencycode = $partialPayment->getCurrencyCode();

                $partialpayment_installment_grid = "";
                $partialpayment_installment_grid .= '<table style="width:100%; border-collapse: collapse; margin-bottom:20px" align="center" cellspacing="0" cellpadding="1" border="0">';
                $partialpayment_installment_grid .= '<tr style="color:#164162;font-size: 16px">';

                $partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;border-top: none;border-left: none;">Installments Amount</th>';
                $partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;border-top: none;">Due Date</th>';
                $partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;border-top: none;">Paid Date</th>';
                $partialpayment_installment_grid .= '<th style ="border: 1px solid; padding: 10px 5px;text-align:center;border-top: none;border-right: none;">Installments Status</th>';
                $partialpayment_installment_grid .= '</tr>';



                if ($installment->getInstallmentStatus() == 1) {
                    $status = '<div style="text-align:center;width: 92px !important;padding: 3px 0; color:#FFF;background-color:#86cae4;border-radius:8px;">Processing</div>';
                    $paidDate = date('Y-m-d', strtotime($installment->getInstallmentPaidDate()));
                } elseif ($installment->getInstallmentStatus() == 2) {
                    $status = '<div style="text-align:center;width: 92px !important;padding: 3px 0; color:#FFF;background-color:#f7944b;border-radius:8px;">Paid</div>';
                    $paidDate = date('Y-m-d', strtotime($installment->getInstallmentPaidDate()));
                } else {
                    $status = '<div style="text-align:center;width: 92px !important;padding: 3px 0; color:#FFF;background-color:#434a56;border-radius:8px;">Pending</div>';
                    $paidDate = 'N / A';
                }

                $partialpayment_installment_grid .= '<tr>';
                $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: 0;border-left: none;" align="center">' . $this->partialHelper->getFormattedPrice($currencycode,$installment->getInstallmentAmount()) . '</td>';
                $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: 0;" align="center">' . date('Y-m-d', strtotime($installment->getInstallmentDueDate())) . '</td>';
                $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: 0;" align="center">' . $paidDate . '</td>';
                $partialpayment_installment_grid .= '<td style ="border: 1px solid;padding: 10px 5px;border-bottom: 0;border-right: none;" align="center">' . $status . '</td>';
                $partialpayment_installment_grid .= '</tr>';
                $partialpayment_installment_grid .= '</table>';

                $params = [
                    'customer_name' => $partialPayment->getCustomerName(),
                    'order_id' => $partialPayment->getOrderId(),
                    'partialpayment_installment_grid' => $partialpayment_installment_grid,
                    'installment_amount' => $this->partialHelper->getFormattedPrice($currencycode,$installment->getInstallmentAmount()),
                    'due_date' => date('Y-m-d', strtotime($installment->getInstallmentDueDate()))

                ];
                $storeScope = ScopeInterface::SCOPE_STORE;

                $sender = $this->scopeConfig->getValue("partialpro/email/$templateKey/identity", $storeScope);
                $admintemplateConfigPath = "partialpro/email/$templateKey/template";
                $admintemplate = $this->scopeConfig->getValue($admintemplateConfigPath, $storeScope);

                $inlineTranslation = $this->inlineTranslation;
                $inlineTranslation->suspend();
                $transportBuilder = $this->transportBuilder;
                if ($this->scopeConfig->getValue("partialpro/email/$templateKey/copy", $storeScope)) {
                    foreach (explode(',', $this->scopeConfig->getValue("partialpro/email/$templateKey/copy", $storeScope)) as $bcc) {
                        $transportBuilder->addBcc($bcc, 'Installments Reminder.');
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
                        ->addTo($partialPayment->getCustomerEmail())
                        ->getTransport();
                    $transport->sendMessage();
                } catch (\Exception $e) {
                    \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());
                }
            }
        }
    }
}
