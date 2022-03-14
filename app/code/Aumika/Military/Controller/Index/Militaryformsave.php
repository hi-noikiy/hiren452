<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aumika\Military\Controller\Index;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\RequestFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\MessageInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Militaryformsave extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
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
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        RequestFactory $requestFactory,
        CustomerExtractor $customerExtractor,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $customerAccountManagement,
        Session $customerSession,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_messageManager = $messageManager;
        $this->requestFactory = $requestFactory;
        $this->customerExtractor = $customerExtractor;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->session = $customerSession;
        $this->resultRedirectFactory = $resultRedirectFactory;

        return parent::__construct($context);
    }
    public function execute()
    {
        $contactinfo = $this->getRequest()->getPost();
        $result = $this->resultJsonFactory->create();
        $request = $this->requestFactory->create();
        try {
            $customerData = [
                'firstname' => $contactinfo['name'],
                'lastname' => $contactinfo['lastname'],
                'email' => $contactinfo['email']
            ];
            $password = $contactinfo['password'];
            $request->setParams($customerData);
            $customer = $this->customerExtractor->extract('customer_account_create', $request);
            $customer = $this->customerAccountManagement->createAccount($customer, $password);
            $customer->setCustomAttribute('military_document', $contactinfo['pllegelsignature']);
            $this->_customerRepositoryInterface->save($customer);
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->session->setCustomerDataAsLoggedIn($customer);
            $this->_messageManager->addSuccess(__('Your Account is created successfully and your Military Document is under Review'));
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            $url = $baseUrl.'customer/account/';
            $resultRedirect->setUrl($url);
            return $resultRedirect;
        } catch (\Exception $e) {
            $result->setData(['error' => 'your customer is not created' . $e]);
            $this->_messageManager->addSuccess(__($e));
        }
        return $result;
    }
}
