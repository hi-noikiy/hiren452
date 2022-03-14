<?php
namespace BT\News\Model\ResourceModel;

/**
 * Class News
 * @package Kitchen365\News\Model\ResourceModel
 */
class News extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('aumika_news', 'id');
    }
}
