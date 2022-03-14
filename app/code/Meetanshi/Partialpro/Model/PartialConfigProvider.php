<?php

namespace Meetanshi\Partialpro\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;

class PartialConfigProvider implements ConfigProviderInterface
{
    protected $dataHelper;
    protected $checkoutSession;
    protected $logger;

    public function __construct(
        Data $dataHelper,
        Session $checkoutSession,
        LoggerInterface $logger
    )
    {
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    public function getConfig()
    {
        $partialConfig = [];
        $enabled = $this->dataHelper->isModuleEnabled();
        $minimumOrderAmount = $this->dataHelper->getMinimumOrderAmount();
        $quote = $this->checkoutSession->getQuote();
        $subtotal = $quote->getSubtotal();

        $partialConfig['fee_label'] = $this->dataHelper->getPartialInstallmentFeeLabel();
        $partialConfig['amt_pay_now_label'] = $this->dataHelper->getAmtPayNowLabel();
        $partialConfig['amt_pay_later_label'] = $this->dataHelper->getAmtPayLaterLabel();
        $partialConfig['show_hide_partial_block'] = ($enabled && ($minimumOrderAmount <= $subtotal)) ? true : false;

        return $partialConfig;
    }
}
