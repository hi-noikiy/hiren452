<?php

namespace Aumika\Military\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Customer extends \Magento\Framework\App\Action\Action implements HttpGetActionInterface
{
    /**
     * Index action
     *
     * @return void
     */
    protected $_pageFactory;

    /**
     * @var Session
     */
    protected $session;

    protected $_customer;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Customer $customer,
        Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_customer = $customer;
        $this->session = $customerSession;
        return parent::__construct($context);
    }
    public function execute()
    {
        $this->_view->loadLayout(); 
        $this->_view->renderLayout(); 
    }
}
