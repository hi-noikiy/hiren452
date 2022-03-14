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


class Cancel extends Action
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

                    if ($partialPayment->getId()) {
                        $installment = $this->installments->load($data['installment_id']);
                        if ($installment->getId()) {
                            $installment->setInstallmentStatus(3)->save();
                        }

                        $this->messageManager->addSuccessMessage(
                            __('Installment cancel successfully.')
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
