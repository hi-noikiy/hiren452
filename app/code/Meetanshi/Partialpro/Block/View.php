<?php

namespace Meetanshi\Partialpro\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as Installments;
use Meetanshi\Partialpro\Model\Partialpayment as Partial;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Customer\Model\SessionFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\Order;
use Meetanshi\Partialpro\Helper\Data;

class View extends Template
{
    protected $partialInstallmentCollection;
    protected $priceHepler;
    protected $customer;
    protected $quoteFactory;
    protected $partial;
    protected $scopeConfig;
    protected $dataHelper;

    public function __construct(
        Context $context,
        Installments $partialInstallmentCollection,
        SessionFactory $customer,
        QuoteFactory $quote,
        Order $order,
        Partial $partial,
        Data $dataHelper,
        priceHelper $priceHepler
    )
    {

        $this->partialInstallmentCollection = $partialInstallmentCollection;
        $this->priceHepler = $priceHepler;
        $this->customer = $customer;
        $this->order = $order;
        $this->partialpro = $partial;
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
        $this->quoteFactory = $quote;
    }

    public function getOrderCollection()
    {
        $partialPaymentId = $this->getRequest()->getParam('profile');
        $collection = $this->partialInstallmentCollection->create();
        $collection->addFieldToFilter('partial_payment_id', $partialPaymentId);
        return $collection;
    }

    public function tempQuote()
    {
        return $this->quoteFactory->create()->load($this->getOrder()->getQuoteId());
    }

    public function getOrder()
    {
        return $this->order->loadByIncrementId($this->getOrderId());
    }

    public function getFormattedPrice($price)
    {
        $currencyCode = $this->getPartialCurrencyCode();
        return $this->dataHelper->getFormattedPrice($currencyCode, $price);
    }

    public function getFormAction()
    {
        return $this->getUrl('partialpayment/account/installmentpay', ['_secure' => true]);
    }

    public function getPartialPaymentId()
    {
        return $this->getRequest()->getParam('profile');
    }

    public function getPartialCurrencyCode()
    {
        $partialPaymentId = $this->getRequest()->getParam('profile');
        $infoCollFirstItem = $this->partialpro->load($partialPaymentId);
        return $infoCollFirstItem->getCurrencyCode();
    }

    public function getOrderId()
    {
        $partialPaymentId = $this->getRequest()->getParam('profile');
        $infoCollFirstItem = $this->partialpro->load($partialPaymentId);
        return $infoCollFirstItem->getOrderId();
    }

    public function getOrderPaymentTitle($paymentMethod)
    {
        return $this->scopeConfig->getValue('payment/' . $paymentMethod . '/title');
    }

    public function getShowPaymentMethod()
    {
        $partialPaymentId = $this->getRequest()->getParam('profile');
        $collection = $this->partialInstallmentCollection->create();
        $collection->addFieldToFilter('partial_payment_id', $partialPaymentId);
        $collection->addFieldToFilter('installment_status', 0);
        if ($collection->count() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
}
