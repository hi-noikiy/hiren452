<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Mediaappearance
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Mediaappearance\Model\ResourceModel;

class Videoblocks extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /*     * ---Functions--- */

    /**
     * Store model
     *
     * @var null|\Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Resource\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_objectManager = $objectManager;
    }

    public function _construct()
    {
        $this->_init('media_blocks', 'media_block_id');
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param   string $identifier
     * @param   int $storeId
     * @return  int
     */
    public function checkIdentifier($identifier, $storeId = null)
    {
        $select = $this->getConnection()->select()->from(['main_table' => $this->getMainTable()], 'media_block_id')
                ->where('main_table.block_identifier = ?', $identifier)
                ->where('main_table.block_status = 1');

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * load
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @param  $value
     * @param  $field
     * @return array
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {

        if (!intval($value) && is_string($value)) {
            $field = 'block_identifier';
        }
        return parent::load($object, $value, $field);
    }

    /**
     * Process page data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {

        $condition = $this->getConnection()->quoteInto('media_block_id = ?', $object->getId());
        $links = $object->getData("product_id");
        //Get Related Media
        if (isset($links)) {
            $mediaIds = $links;
            $this->getConnection()->delete($this->getTable('media_block_videos'), $condition);
            //Save Related Articles
            foreach ($mediaIds as $_media) {
                $mediaArray = [];
                $mediaArray['media_block_id'] = $object->getId();
                $mediaArray['mediagallery_id'] = $_media;
                $this->getConnection()->insert($this->getTable('media_block_videos'), $mediaArray);
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Check for unique of identifier of block.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function getIsUniqueBlockToStores(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select()
                ->from($this->getMainTable())
                ->join(['cbs' => $this->getTable('cms/block_store')], $this->getMainTable() . '.block_id = `cbs`.block_id')
                ->where($this->getMainTable() . '.identifier = ?', $object->getData('identifier'));
        if ($object->getId()) {
            $select->where($this->getMainTable() . '.block_id <> ?', $object->getId());
        }
        $select->where('`cbs`.store_id IN (?)', (array) $object->getData('stores'));

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }

        return true;
    }
}
