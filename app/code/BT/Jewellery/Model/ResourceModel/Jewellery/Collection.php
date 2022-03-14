<?php
namespace BT\Jewellery\Model\ResourceModel\Jewellery;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BT\Jewellery\Model\Jewellery', 'BT\Jewellery\Model\ResourceModel\Jewellery');
    }
}
