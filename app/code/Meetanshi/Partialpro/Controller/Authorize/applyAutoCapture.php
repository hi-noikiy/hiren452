<?php

namespace Meetanshi\Partialpro\Controller\Authorize;

use Magento\Framework\Controller\ResultFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Meetanshi\Partialpro\Helper\Data as partialHelper;
use Magento\Framework\App\Action\Context;

class applyAutoCapture extends \Magento\Framework\App\Action\Action
{
    protected $checkoutSession;
    protected $partialHelper;

    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        partialHelper $partialHelper
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->partialHelper = $partialHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $quote = $this->checkoutSession->getQuote();
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result = 0;
        if ($this->partialHelper->isModuleEnabled() && $this->partialHelper->getAutoCapture() && $quote->getPartialPayLater() > 0) {
            $result = 1;
        }
        $resultJson->setData($result);
        return $resultJson;
    }
}
