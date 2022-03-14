<?php

namespace Meetanshi\Partialpro\Model;

use Magento\Framework\Model\AbstractModel;

class Installments extends AbstractModel
{
    public function _construct()
    {
        $this->_init('Meetanshi\Partialpro\Model\ResourceModel\Installments');
    }
}
