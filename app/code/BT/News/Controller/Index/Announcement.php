<?php

namespace BT\News\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Announcement extends \Magento\Framework\App\Action\Action implements HttpGetActionInterface
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

    protected $_newsCollection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Customer $customer,
        \BT\News\Model\ResourceModel\News\CollectionFactory $newsCollection,
        Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_newsCollection = $newsCollection;
        $this->_pageFactory = $pageFactory;
        $this->_customer = $customer;
        $this->session = $customerSession;
        return parent::__construct($context);
    }
    public function execute()
    {
        return $this->_pageFactory->create();
    }
}
