<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductPagePdf\Model\ResourceModel\Profile;

use Magento\Store\Model\Store;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var int
     */
    protected $_idFieldName = 'profile_id';

    /**
     * @var \Magento\Framework\Data\Collection\EntityFactory
     */
    protected $entityFactory;

    /**
     * @var \Magento\Framework\Data\Collection\Db\FetchStrategyInterface
     */
    protected $fetchStrategy;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface 
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected $resource;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
        $this->date = $date;
    }

    protected function _construct()
    {
        $this->_init(\Magezon\ProductPagePdf\Model\Profile::class, \Magezon\ProductPagePdf\Model\ResourceModel\Profile::class);
        $this->_map['fields']['store_id'] = 'store.store_id';
        $this->_map['fields']['profile_id'] = 'main_table.profile_id';
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinTable('store', 'mgz_productpagepdf_profile_store', 'profile_id');
    }

    /**
     * @param $alias
     * @param $tableName
     * @param $linkField
     */
    protected function joinTable($alias, $tableName, $linkField)
    {
        $this->getSelect()->joinLeft(
            [$alias => $this->getTable($tableName)],
            'main_table.' . $linkField . ' = ' . $alias . '.' . $linkField,
            []
        )->group(
            'main_table.' . $linkField
        );
        parent::_renderFiltersBefore();
    }

    /**
     * After collection load
     *
     * @return \Magezon\ProductPagePdf\Model\ResourceModel\Profile\Collection
     */
    protected function _afterLoad()
    {
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_after', [$this->_eventObject => $this]);
        $this->performAfterLoad('mgz_productpagepdf_profile_store', 'profile_id', 'store_id');
        return parent::_afterLoad();
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function performAfterLoad($tableName, $linkField, $field)
    {
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['productpagepdf_entity_store' => $this->getTable($tableName)])
                ->where('productpagepdf_entity_store.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData[$field];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $storeIdKey = array_search(Store::DEFAULT_STORE_ID, $storesData[$linkedId], true);
                    if ($storeIdKey !== false) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = current($storesData[$linkedId]);
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData($field, $storesData[$linkedId]);
                }
            }
        }
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return \Magezon\ProductPagePdf\Model\ResourceModel\File\Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);
        return $this;
    }

    /**
     * Perform adding filter by store
     *
     * @param int|array|Store $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store_id', ['in' => $store], 'public');
    }

    /**
     * @return \Magezon\ProductPagePdf\Model\ResourceModel\Profile\Collection
     */
    public function prepareCollection() 
    {
        $date = $this->date->gmtDate('Y-m-d');
        $store = $this->storeManager->getStore();
        $this->addFieldToFilter('main_table.is_active', \Magezon\ProductPagePdf\Model\Profile::STATUS_ENABLED);
        $this->addStoreFilter($store);
        $this->addFieldToFilter('from_date', [
                'or' => [
                    0 => ['lteq' => $date],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ])
            ->addFieldToFilter('to_date', [
                'or' => [
                    0 => ['gt' => $date],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ]);
        return $this;
    }
}
