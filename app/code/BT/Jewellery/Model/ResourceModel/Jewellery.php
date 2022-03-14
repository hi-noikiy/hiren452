<?php
namespace BT\Jewellery\Model\ResourceModel;


class Jewellery extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * News constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * init category gallory
     */
    protected function _construct()
    {
        $this->_init('aumika_jewellery', 'id');
    }
}
