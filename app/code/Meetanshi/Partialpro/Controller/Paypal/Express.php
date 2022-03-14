<?php

namespace Meetanshi\Partialpro\Controller\Paypal;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Meetanshi\Partialpro\Model\Api\Nvp;
use Magento\Paypal\Model\Config;

class Express extends Action\Action
{
    protected $resultPageFactory;
    protected $partialpaymentCron;
    protected $paypalNvp;
    protected $paypalConfig;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Nvp $paypalNvp,
        Config $paypalConfig
    )
    {

        $this->resultPageFactory = $resultPageFactory;
        $this->paypalNvp = $paypalNvp;
        $this->paypalConfig = $paypalConfig;
        $this->paypalConfig->setMethod('paypal_express');
        parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->paypalNvp->callInstallmentSetExpressCheckout();
        $redirectUrl = $this->paypalConfig->getExpressCheckoutStartUrl($response['TOKEN']);
        return $this->resultRedirectFactory->create()->setUrl($redirectUrl);
    }
}
