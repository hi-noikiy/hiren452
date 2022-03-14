<?php

namespace Meetanshi\Partialpro\Block\Adminhtml\Manage;

use Magento\Backend\Block\Template;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\Serialize\Serializer\Json;
use Meetanshi\Partialpro\Model\Partialpayment;
use Magento\Sales\Model\Order;
use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as Installments;
use Magento\Quote\Model\QuoteFactory;
use Meetanshi\Partialpro\Helper\Data;

class Edit extends Template
{
    protected $layout;
    protected $request;
    protected $partialPayment;
    protected $urlBuilder;
    protected $timezone;
    protected $scopeConfig;
    protected $localeCurrency;
    protected $order;
    protected $partialInstallmentCollection;
    protected $quoteFactory;
    protected $dataHelper;
    protected $serialize;

    public function __construct(
        Template\Context $context,
        CurrencyInterface $localeCurrency,
        Installments $partialInstallmentCollection,
        Timezone $timezone,
        Partialpayment $paymentFactory,
        QuoteFactory $quote,
        Order $order,
        Data $dataHelper,
        Json $serialize
    )
    {

        parent::__construct($context);
        $this->partialInstallmentCollection = $partialInstallmentCollection;
        $this->layout = $context->getLayout();
        $this->request = $context->getRequest();
        $this->scopeConfig = $context->getScopeConfig();
        $this->partialPayment = $paymentFactory;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->timezone = $timezone;
        $this->localeCurrency = $localeCurrency;
        $this->serialize = $serialize;
        $this->order = $order;
        $this->quoteFactory = $quote;
        $this->dataHelper = $dataHelper;
    }

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Meetanshi_Partialpro::summary/view.phtml');
    }

    public function getPartialPayment()
    {
        $id = $this->request->getParam('id', null);
        $paymentOption = null;
        if ($id) {
            $paymentOption = $this->partialPayment->load($id);
        }
        return $paymentOption;
    }

    public function getHelperData()
    {
        return $this->dataHelper;
    }

    public function getFormAction()
    {
        return $this->getUrl('*/*/installmentpay', ['_secure' => true]);
    }

    public function getInstallmentCollection()
    {
        $partialPaymentId = $this->request->getParam('id');
        $collection = $this->partialInstallmentCollection->create();
        $collection->addFieldToFilter('partial_payment_id', $partialPaymentId);
        return $collection;
    }

    public function getOrder()
    {
        return $this->order->loadByIncrementId($this->getOrderId());
    }

    public function tempQuote()
    {
        return $this->quoteFactory->create()->load($this->getOrder()->getQuoteId());
    }

    public function getOrderId()
    {
        $partialPaymentId = $this->request->getParam('id');
        $item = $this->partialPayment->load($partialPaymentId);
        return $item->getOrderId();
    }

    public function getPartialPaymentId()
    {
        return $this->request->getParam('id', null);
    }

    public function getTitle()
    {
        if ($extOrderId = $this->getOrder()->getExtOrderId()) {
            $extOrderId = '[' . $extOrderId . '] ';
        } else {
            $extOrderId = '';
        }

        return sprintf('Partial Payment - Order # %s %s | %s', $this->getOrder()->getRealOrderId(), $extOrderId, $this->timezone->formatDate($this->getOrder()->getCreatedAt(), \IntlDateFormatter::MEDIUM, true));
    }

    public function getCustomerViewUrl()
    {
        if ($this->getOrder()->getCustomerIsGuest() || !$this->getOrder()->getCustomerId()) {
            return false;
        }

        return $this->urlBuilder->getUrl('customer/index/edit', ['id' => $this->getOrder()->getCustomerId()]);
    }

    public function getViewUrl($orderId)
    {
        return $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    public function getStore($id = null)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $manager = $om->get('Magento\Store\Model\StoreManagerInterface');
        return $manager->getStore($id);
    }

    public function getOrderPaymentTitle($paymentMethod)
    {
        return $this->scopeConfig->getValue('payment/' . $paymentMethod . '/title');
    }

    public function getCurrencySymbol($code)
    {
        $currSymb = $this->localeCurrency->getCurrency($code)->getSymbol();
        if ($currSymb == null) {
            $currSymb = $code;
        }
        return $currSymb;
    }

    public function getShowPaymentMethod()
    {
        $collection = $this->getInstallmentCollection();
        $collection->addFieldToFilter('installment_status', 0);
        if ($collection->count() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getUnserializeData($code)
    {
        return $this->serialize->unserialize($code);
    }
}
