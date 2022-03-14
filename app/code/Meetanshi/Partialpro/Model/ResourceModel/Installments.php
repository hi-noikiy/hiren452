<?php

namespace Meetanshi\Partialpro\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Installments extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('meetanshi_installment_summary', 'installment_id');
    }
}
