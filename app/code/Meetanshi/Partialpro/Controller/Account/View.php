<?php

namespace Meetanshi\Partialpro\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as Installments;

class View extends Action
{
    protected $partialInstallmentCollection;
    protected $resultPageFactory;
    protected $session;

    public function __construct(
        Context $context,
        Session $customerSession,
        Installments $partialInstallmentCollection,
        PageFactory $resultPageFactory
    ) {
    
        $this->session = $customerSession;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->partialInstallmentCollection = $partialInstallmentCollection;
    }

    public function execute()
    {
        if (!$this->session->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        } else {
            $partialPaymentId = $this->getRequest()->getParam('profile');
            if (!($partialPaymentId)) {
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('partialpayment/account/index');
                $this->messageManager->addErrorMessage('Order Not Found.');
                return $resultRedirect;
            } else {
                $collection = $this->partialInstallmentCollection->create();
                $collection->addFieldToFilter('partial_payment_id', $partialPaymentId);
                if ($collection->count() <= 0) {
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('partialpayment/account/index');
                    $this->messageManager->addErrorMessage('Order Not Found.');
                    return $resultRedirect;
                }
            }

            $resultPage = $this->resultPageFactory->create();
            $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('partialpayment/account/index');
            }
            $resultPage->getConfig()->getTitle()->set(__('Manage Installments'));
            return $resultPage;
        }
    }
}
