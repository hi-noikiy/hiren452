<?php

namespace Meetanshi\Partialpro\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Partialpayment extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('meetanshi_partial_payment', 'partial_payment_id');
    }
}
