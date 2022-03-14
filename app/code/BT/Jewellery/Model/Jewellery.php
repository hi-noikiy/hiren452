<?php
namespace BT\Jewellery\Model;


class Jewellery extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'jewellery';

    /**
     * @var string
     */
    protected $_eventObject = 'jewellery';

    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * custuct
     */
    protected function _construct()
    {
        $this->_init('BT\Jewellery\Model\ResourceModel\Jewellery');
    }
}
