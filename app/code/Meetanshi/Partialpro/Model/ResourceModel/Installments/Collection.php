<?php

namespace Meetanshi\Partialpro\Model\ResourceModel\Installments;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Meetanshi\Partialpro\Model\Installments', 'Meetanshi\Partialpro\Model\ResourceModel\Installments');
    }
}
