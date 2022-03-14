<?php

namespace Meetanshi\Partialpro\Block\Adminhtml\Manage;

use Magento\Backend\Block\Widget\Context as Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;
use Meetanshi\Partialpro\Model\ResourceModel\Partialpayment\CollectionFactory;
use Meetanshi\Partialpro\Helper\Data;

class Grid extends Extended
{
    protected $ruleCollectionFactory;
    protected $helper;

    public function __construct(
        CollectionFactory $ruleCollectionFactory,
        Data $helper,
        Context $context,
        BackendHelper $backendHelper
    )
    {

        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->helper = $helper;

        parent::__construct($context, $backendHelper);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('todayGrid');
        $this->setDefaultSort('pos');
    }

    protected function _prepareCollection()
    {
        $collection = $this->ruleCollectionFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('partial_payment_id', [
            'header' => __('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'partial_payment_id',
        ]);

        $this->addColumn('order_id', [
            'header' => __('Order ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'order_id',
        ]);

        $this->addColumn('customer_name', [
            'header' => __('Customer Name'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'customer_name',
        ]);

        $this->addColumn('customer_email', [
            'header' => __('Customer Email'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'customer_email',
        ]);

        $this->addColumn('order_amount', [
            'header' => __('Order Amount'),
            'align' => 'right',
            'index' => 'order_amount',
            'renderer' => '\Meetanshi\Partialpro\Block\Adminhtml\Grid\Renderer\Amount',
        ]);

        $this->addColumn('paid_amount', [
            'header' => __('Paid Amount'),
            'align' => 'right',
            'index' => 'paid_amount',
            'renderer' => '\Meetanshi\Partialpro\Block\Adminhtml\Grid\Renderer\Amount',
        ]);

        $this->addColumn('remaining_amount', [
            'header' => __('Remaining Amount'),
            'align' => 'right',
            'index' => 'remaining_amount',
            'renderer' => '\Meetanshi\Partialpro\Block\Adminhtml\Grid\Renderer\Amount',
        ]);

        $this->addColumn('payment_status', [
            'header' => __('Payment Status'),
            'align' => 'right',
            'index' => 'payment_status',
            'renderer' => '\Meetanshi\Partialpro\Block\Adminhtml\Grid\Renderer\Status',
        ]);

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/index', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}
