<?php

namespace Meetanshi\Partialpro\Controller\Paypal;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Meetanshi\Partialpro\Model\Api\Nvp;

class Cancel extends Action\Action
{
    protected $resultPageFactory;
    protected $partialpaymentCron;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Nvp $PaypalNvp
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->PaypalNvp = $PaypalNvp;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->messageManager->addError(__('Payment Transaction request rejected by paypal.'));
        $this->_redirect('partialpayment/account/index');
    }
}
