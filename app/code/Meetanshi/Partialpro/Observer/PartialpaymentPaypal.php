<?php

namespace Meetanshi\Partialpro\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Meetanshi\Partialpro\Helper\Data;

class PartialpaymentPaypal implements ObserverInterface
{
    public $checkout;
    protected $dataHelper;

    public function __construct(
        Session $checkout,
        Data $dataHelper
    )
    {
        $this->checkout = $checkout;
        $this->dataHelper = $dataHelper;
    }

    public function execute(Observer $observer)
    {
        $cart = $observer->getEvent()->getCart();
        $quote = $this->checkout->getQuote();

        if ($fee = $quote->getPartialPayLater()) {
            $fee = $this->dataHelper->convertCurrency($quote->getBaseCurrencyCode(), $quote->getQuoteCurrencyCode(), $fee);
            $cart->addCustomItem($this->dataHelper->getAmtPayLaterLabel(), 1, -$fee);
        }
        if ($partialInstallmentFee = $quote->getPartialInstallmentFee()) {
            $partialInstallmentFee = $this->dataHelper->convertCurrency($quote->getBaseCurrencyCode(), $quote->getQuoteCurrencyCode(), $partialInstallmentFee);
            $cart->addCustomItem($this->dataHelper->getPartialInstallmentFeeLabel(), 1, $partialInstallmentFee);
        }
        return $this;
    }
}
