<?php
namespace Hiddentechies\Reviewspro\Model\ResourceModel;

class Reviewspro extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('hidden_reviewspro', 'id');
    }
}
?>