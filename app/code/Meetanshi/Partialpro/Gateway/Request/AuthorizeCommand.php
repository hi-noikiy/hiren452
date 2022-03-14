<?php

namespace Meetanshi\Partialpro\Gateway\Request;

use Magento\Sales\Model\Order\Payment\State\AuthorizeCommand as coreAuthorizeCommand;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\StatusResolver;

class AuthorizeCommand
{
    private $helper;
    private $checkoutSession;

    public function __construct(
        Data $helper,
        CheckoutSession $checkoutSession
    )
    {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
    }

    public function aroundExecute(coreAuthorizeCommand $subject, \Closure $proceed, $payment, $amount, $order)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('aroundExecute call');

        try {
            if ($this->helper->isModuleEnabled()) {
                if ($this->helper->getPartialProductSet($order->getQuoteId())) {
                    if ($order->getPartialPayNow() > 0) {
                        $amount = $order->getPartialPayNow();
                    }
                }
            }

            $statusResolver = ObjectManager::getInstance()->get(StatusResolver::class);

            $state = Order::STATE_PROCESSING;
            $status = null;
            $message = 'Authorized amount of %1.';

            if ($payment->getIsTransactionPending()) {
                $state = Order::STATE_PAYMENT_REVIEW;
                $message = 'We will authorize %1 after the payment is approved at the payment gateway.';
            }

            if ($payment->getIsFraudDetected()) {
                $state = Order::STATE_PAYMENT_REVIEW;
                $status = Order::STATUS_FRAUD;
                $message .= ' Order is suspended as its authorizing amount %1 is suspected to be fraudulent.';
            }

            if (!isset($status)) {
                $status = $statusResolver->getOrderStatusByState($order, $state);
            }

            $order->setState($state);
            $order->setStatus($status);

            return __($message, $order->getBaseCurrency()->formatTxt($amount));

        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }


    }
}