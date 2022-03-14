<?php

namespace Meetanshi\Partialpro\Block\Adminhtml\Reports;

use Magento\Backend\Block\Widget\Grid\Container;

class Revenue extends Container
{
    protected $_template = 'report/grid/container.phtml';

    protected function _construct()
    {
        $this->_blockGroup = 'Meetanshi_Partialpro';
        $this->_controller = 'adminhtml_reports_revenue';
        parent::_construct();
        $this->_headerText = __('Revenue Report');
        $this->buttonList->remove('add');
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filterFormSubmit()', 'class' => 'primary']
        );
    }
}