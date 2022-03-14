<?php

namespace Meetanshi\Partialpro\Model;

use Magento\Framework\Model\AbstractModel;

class Partialpayment extends AbstractModel
{
    public function _construct()
    {
        $this->_init('Meetanshi\Partialpro\Model\ResourceModel\Partialpayment');
    }
}
