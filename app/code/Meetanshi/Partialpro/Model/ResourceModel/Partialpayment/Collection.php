<?php

namespace Meetanshi\Partialpro\Model\ResourceModel\Partialpayment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'partial_payment_id';
    /**
     * @var string
     */
    protected $_eventPrefix = 'meetanshi_partial_collection';
    /**
     * @var string
     */
    protected $_eventObject = 'meetanshi_partial_collection';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Meetanshi\Partialpro\Model\Partialpayment', 'Meetanshi\Partialpro\Model\ResourceModel\Partialpayment');
    }
}
