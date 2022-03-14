<?php
namespace Hiddentechies\Reviewspro\Model;

class Reviewspro extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Hiddentechies\Reviewspro\Model\ResourceModel\Reviewspro');
    }
}
?>