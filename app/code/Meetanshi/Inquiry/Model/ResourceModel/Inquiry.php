<?php

namespace Meetanshi\Inquiry\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Inquiry extends AbstractDb
{
    protected $_idFieldName = 'dealer_id';

    protected function _construct()
    {
        $this->_init('dealer_inquiry', 'dealer_id');
    }
}
