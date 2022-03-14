<?php
namespace BT\News\Model;

/**
 * Class News
 * @package Kitchen365\news\Model
 */
class News extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'news';

    /**
     * @var string
     */
    protected $_eventObject = 'news';

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
        $this->_init('BT\News\Model\ResourceModel\News');
    }
}
