<?php

namespace Meetanshi\Partialpro\Controller\Paypal;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\Http;

class ReturnAction extends Action\Action
{
    protected $resultPageFactory;
    protected $request;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Http $request
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->request->getParam('token') && $this->request->getParam('PayerID')) {
            $this->_forward('success');
        } else {
            $this->_forward('cancel');
        }
    }
}
