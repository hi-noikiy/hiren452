<?php

namespace Meetanshi\Partialpro\Controller\Tagpay;

use Magento\Framework\App\Action;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\Order;
use Meetanshi\Partialpro\Helper\Tagpay as TagpayHelper;

class TagpayRedirect extends Action\Action
{
    protected $helper;
    protected $checkoutSession;
    protected $jsonFactory;
    protected $tagpayHelper;

    public function __construct(
        Action\Context $context,
        CheckoutSession $checkoutSession,
        JsonFactory $resultJsonFactory,
        TagpayHelper $tagpayHelper,
        Data $helper
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->jsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->tagpayHelper = $tagpayHelper;
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

                    $html = $this->tagpayHelper->getPaymentForm($order, $purchaseRef, $currency, $amount, 0);

                    return $result->setData(['error' => false, 'success' => true, 'html' => $html]);
                } catch (\Exception $e) {
                    $this->checkoutSession->restoreQuote();
                    return $result->setData(['error' => true, 'success' => false, 'message' => __($e->getMessage())]);
                }
            }
            return false;
        }
    }
}
