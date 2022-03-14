<?php

namespace Meetanshi\Partialpro\Block\Adminhtml\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\Input;
use Magento\Framework\DataObject;

class Status extends Input
{
    function render(DataObject $row)
    {
        $state = $row->getData($this->getColumn()->getIndex());
        if ($state == 1) {
            $colour = "86cae4";
            $value = "Processing";
        } elseif ($state == 2) {
            $colour = "f7944b";
            $value = "Paid";
        } else {
            $colour = "434a56";
            $value = "Pending";
        }
        return '<div style="text-align:center;width: 110px !important;    padding: 5px 0; color:#FFF;background:#' . $colour . ';border-radius:8px;width:100%">' . $value . '</div>';
    }
}
