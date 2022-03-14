<?php

namespace Meetanshi\Partialpro\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Manage extends Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_manage';
        $this->_blockGroup = 'Meetanshi_Partialpro';
        $this->_headerText = __('Manage Partial Payment Orders');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
