<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aumika\Military\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Afterloginmilitary extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    protected $_pageFactory;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var CustomerExtractor
     */
    protected $customerExtractor;

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    protected $addressRepository;

    protected $addressDataFactory;

    protected $helperData;
    protected $_messageManager;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    protected $resource;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory
    ) {
        $this->_messageManager = $messageManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->session = $customerSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->customer = $customer;
        $this->customerFactory = $customerFactory;

        return parent::__construct($context);
    }
    public function execute()
    {
        $contactinfo = $this->getRequest()->getPost();
        $result = $this->resultJsonFactory->create();
        try {
            $currentCustomer = $this->session->getCustomer();
            $customer = $this->customer->load($currentCustomer->getId());
            $customerData = $customer->getDataModel();
            $customerData->setCustomAttribute('military_document',$contactinfo['pllegelsignature']);
            $customer->updateData($customerData);
            $customerResource = $this->customerFactory->create();
            $customerResource->saveAttribute($customer, 'military_document');

            $resultRedirect = $this->resultRedirectFactory->create();
            $this->_messageManager->addSuccess(__('Your Military Document Has been Updated successfully and your Military Document is under Review'));
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            $url = $baseUrl.'military/index/customer/';
            $resultRedirect->setUrl($url);
            return $resultRedirect;
        } catch (\Exception $e) {
            $result->setData(['error' => 'your customer is not created' . $e]);
            $this->_messageManager->addSuccess(__($e));
        }
        return $result;
    }
}
