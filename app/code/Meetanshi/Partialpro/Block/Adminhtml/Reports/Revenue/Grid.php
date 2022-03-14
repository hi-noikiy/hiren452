<?php

namespace Meetanshi\Partialpro\Block\Adminhtml\Reports\Revenue;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Reports\Helper\Data as reportData;
use Magento\Reports\Model\Grouped\CollectionFactory;
use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as installmentCollection;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Reports\Controller\Adminhtml\Report\AbstractReport as initRerpot;

class Grid extends Extended
{
    protected $_collectionFactory;
    protected $installmentCollection;
    protected $initRerpot;

    public function __construct(
        Context $context,
        Data $backendHelper,
        reportData $reportsData,
        CollectionFactory $collectionFactory,
        installmentCollection $installmentCollection,
        initRerpot $initRerpot,
        array $data = []
    )
    {
        $this->_collectionFactory = $collectionFactory;
        $this->initRerpot = $initRerpot;
        $this->installmentCollection = $installmentCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    protected $_countTotals = true;
    protected $_columnGroupBy = 'period';

    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
        $this->setFilterVisibility(false);
        $this->setUseAjax(false);
        $this->initRerpot->_initReportAction($this);
    }

    public function getCollection()
    {
        if ($this->_collection === null) {
            $this->setCollection($this->_collectionFactory->create());
        }
        return $this->_collection;
    }

    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();
        if ($filterData->getData('from') == null || $filterData->getData('to') == null) {
            $this->setCountTotals(false);
            $this->setCountSubTotals(false);
            return parent::_prepareCollection();
        }

        $from = $filterData->getData('from', null) . ' 00:00:00';
        $to = $filterData->getData('to', null) . ' 23:59:59';

        $collection2 = $this->installmentCollection->create();
        $collection2->addFieldToFilter('installment_paid_date', ['lteq' => $to]);
        $collection2->addFieldToFilter('installment_paid_date', ['gteq' => $from]);

        $this->setCollection($collection2);
        return $this;
    }

    public function getTotals()
    {
        $totals = new \Magento\Framework\DataObject();
        $fields = array(
            'installment_amount' => 0
        );
        foreach ($this->getCollection() as $item) {
            foreach ($fields as $field => $value) {
                $fields[$field] += $item->getData($field);
            }
        }
        $totals->setData($fields);
        return $totals;
    }


    protected function _prepareColumns()
    {
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);
        $this->addColumn(
            'installment_paid_date',
            [
                'header' => __('Interval'),
                'index' => 'installment_paid_date',
                'sortable' => false,
                'filter' => false,
                'period_type' => $this->getPeriodType(),
                'renderer' => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',
                'totals_label' => __('Total'),
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );
        $this->addColumn(
            'installment_id',
            [
                'header' => __('Installment Id'),
                'index' => 'installment_id',
                'total' => 'sum',
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        $this->addColumn(
            'Installment Paid Date',
            [
                'header' => __('Installment Paid Date'),
                'index' => 'installment_paid_date',
                'type' => 'date',
                'total' => 'sum',
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'

            ]
        );
        $this->addColumn(
            'installment_amount',
            [
                'header' => __('Installment Amount'),
                'index' => 'installment_amount',
                'type' => 'currency',
                'currency_code' => $currencyCode,
                'total' => 'sum',
                'sortable' => false,
                'filter' => false,
                'rate' => $rate,
                'header_css_class' => 'col-invoiced',
                'column_css_class' => 'col-invoiced'
            ]
        );

        $this->addExportType('*/*/exportRevenueCsv', __('CSV'));
        $this->addExportType('*/*/exportRevenueExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }
}