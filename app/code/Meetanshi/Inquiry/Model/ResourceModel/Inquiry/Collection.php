<?php

namespace Meetanshi\Inquiry\Model\ResourceModel\Inquiry;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'dealer_id';

    protected function _construct()
    {
        $this->_init('Meetanshi\Inquiry\Model\Inquiry', 'Meetanshi\Inquiry\Model\ResourceModel\Inquiry');
    }
}
