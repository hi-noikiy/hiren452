<?php

namespace Meetanshi\Partialpro\Controller\Orangeivory;

use Magento\Framework\App\Action;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\Order;

class OrangeIvoryRedirect extends Action\Action
{
    protected $helper;
    protected $checkoutSession;
    protected $jsonFactory;

    public function __construct(
        Action\Context $context,
        CheckoutSession $checkoutSession,
        JsonFactory $resultJsonFactory,
        Data $helper
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->jsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->helper->isModuleEnabled()) {

            if ($this->getRequest()->isAjax()) {
                $result = $this->jsonFactory->create();
                try {
                    $order = $this->checkoutSession->getLastRealOrder();
                    $order->setState(Order::STATE_PENDING_PAYMENT, true);
                    $order->setStatus(Order::STATE_PENDING_PAYMENT);
                    $order->save();

                    $amount = $order->getPartialPayNow();
                    if ($amount <= 0) {
                        $amount = $order->getGrandTotal();
                    }
                    $currency = $order->getOrderCurrencyCode();
                    $purchaseRef = $order->getIncrementId();

                    $html = $this->helper->getPaymentForm($purchaseRef, $currency, $amount);

                    return $result->setData(['error' => false, 'success' => true, 'html' => $html]);
                } catch (\Exception $e) {

                    $this->checkoutSession->restoreQuote();
                    return $result->setData(['error' => true, 'success' => false, 'message' => __('Payment exception')]);
                }
            }
            return false;
        }
    }
}
