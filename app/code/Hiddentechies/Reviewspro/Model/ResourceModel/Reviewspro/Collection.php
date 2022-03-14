<?php

namespace Hiddentechies\Reviewspro\Model\ResourceModel\Reviewspro;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Hiddentechies\Reviewspro\Model\Reviewspro', 'Hiddentechies\Reviewspro\Model\ResourceModel\Reviewspro');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>