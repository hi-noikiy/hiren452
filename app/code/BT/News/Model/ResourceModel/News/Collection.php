<?php
namespace BT\News\Model\ResourceModel\News;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BT\News\Model\News', 'BT\News\Model\ResourceModel\News');
    }
}
