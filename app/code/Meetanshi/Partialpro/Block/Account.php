<?php

namespace Meetanshi\Partialpro\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Meetanshi\Partialpro\Model\ResourceModel\Partialpayment\CollectionFactory as Partialpayment;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Customer\Model\SessionFactory;
use Meetanshi\Partialpro\Helper\Data;

class Account extends Template
{
    protected $partialpaymentCollection;
    protected $priceHepler;
    protected $customer;
    protected $dataHelper;

    public function __construct(
        Context $context,
        Partialpayment $partialpaymentCollection,
        SessionFactory $customer,
        Data $dataHelper,
        priceHelper $priceHepler
    )
    {

        $this->partialpaymentCollection = $partialpaymentCollection;
        $this->priceHepler = $priceHepler;
        $this->customer = $customer;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    protected function _prepareLayout()
    {

        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Manage Partial Payment'));

        if ($this->getOrderCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'reward.history.pager'
            )->setAvailableLimit([10 => 10, 10 => 10, 15 => 15, 20 => 20])
                ->setShowPerPage(true)->setCollection(
                    $this->getOrderCollection()
                );
            $this->setChild('pager', $pager);
            $this->getOrderCollection()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getOrderCollection()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;
        $customer = $this->customer->create();
        $collection = $this->partialpaymentCollection->create();
        $collection->addFieldToFilter('customer_id', $customer->getCustomer()->getId());
        $collection->setOrder('partial_payment_id', 'DESC');
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    public function getFormattedPrice($currencyCode, $price)
    {
        return $this->dataHelper->getFormattedPrice($currencyCode, $price);
    }
}
