<?php

namespace Meetanshi\Inquiry\Controller\Adminhtml\Inquiry;

use Meetanshi\Inquiry\Helper\Data;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Registry;
use Meetanshi\Inquiry\Model\InquiryFactory;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Meetanshi\Inquiry\Model\Inquiry;

class Createcustomer extends Action
{
    protected $helper;
    protected $storeManager;
    protected $customerFactory;
    private $coreRegistry;
    private $gridFactory;
    protected $transportBuilder;
    protected $inlineTranslation;
    protected $scopeConfig;
    protected $inquiryModel;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        Registry $coreRegistry,
        InquiryFactory $gridFactory,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        Inquiry $inquiry,
        Data $helper
    )
    {

        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->coreRegistry = $coreRegistry;
        $this->gridFactory = $gridFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->inquiryModel = $inquiry;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $rowId = $this->getRequest()->getParam('id');
        $rowData = $this->gridFactory->create();
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            if (!$rowData->getDealerId()) {
                $this->messageManager->addError(__('Row data no longer exist.'));
                return $this->_redirect('meetanshi_inquiry/inquiry/index');
            }
        }
        try {
            if ($this->helper->getCustomerGroup() != '') :
                $this->coreRegistry->register('row_data', $rowData);
                $storeId = $rowData->getStoreView();
                $store = $this->storeManager->getStore($storeId);
                $websiteId = $store->getWebsiteId();
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($websiteId);
                $customer->setGroupId($this->helper->getCustomerGroup());
                $customer->setEmail($rowData->getEmail());
                $customer->setFirstname($rowData->getFirstName());
                $customer->setLastname($rowData->getLastName());
                $password = $this->generatePassword();
                $customer->setPassword($password);
                $customer->save();
                $model = $this->inquiryModel->load($rowData['dealer_id']);
                $model->setIsCustomerCreated($websiteId);
                $model->save();
                $fromName = $this->helper->getSenderEmailName($this->helper->getCreateCustomerEmailSender());
                $fromEmail = $this->helper->getSenderEmailAddress($this->helper->getCreateCustomerEmailSender());
                $from = ['email' => $fromEmail, 'name' => $fromName];
                $this->inlineTranslation->suspend();
                $to = $rowData->getEmail();
                $templateOptions = [
                    'area' => Area::AREA_ADMINHTML,
                    'store' => $storeId
                ];

                $emailObject = ["firstname" => $rowData->getFirstName(), "lastname" => $rowData->getLastName(), "password" => $password, "email" => $rowData->getEmail()];
                $postObject = new DataObject();
                $postObject->setData($emailObject);
                $transport = $this->transportBuilder->setTemplateIdentifier($this->helper->getCreateCustomerEmailTemplate())
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($from)
                    ->addTo($to)
                    ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                $this->messageManager->addSuccess(__('Customer created successfully and notify the customer by email.'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            endif;
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->helper->printLog($e->getMessage());
            $this->messageManager->addError($e->getMessage());
            return $this->_redirect('meetanshi_inquiry/inquiry/index');
        }
    }

    public function generatePassword()
    {
        $length = 8;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Meetanshi_Inquiry::inquiry');
    }
}
