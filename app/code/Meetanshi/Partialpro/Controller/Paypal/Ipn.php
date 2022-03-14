<?php

namespace Meetanshi\Partialpro\Controller\Paypal;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Meetanshi\Partialpro\Model\Api\Nvp;
use Psr\Log\LoggerInterface;

class Ipn extends Action\Action
{
    protected $resultPageFactory;
    protected $partialpaymentCron;
    protected $_logger;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Nvp $PaypalNvp,
        LoggerInterface $logger
    )
    {
        $this->_logger = $logger;
        $this->resultPageFactory = $resultPageFactory;
        $this->PaypalNvp = $PaypalNvp;
        parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }
        try {
            $data = $this->getRequest()->getPostValue();
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            $this->getResponse()->setHttpResponseCode(500);
        }
    }
}
