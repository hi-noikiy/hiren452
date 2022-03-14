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
namespace FME\Mediaappearance\Model\ResourceModel\Mediaappearance;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'mediagallery_id';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $_previewFlag;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;
    }

    /**
     * _construct
     *
     */
    public function _construct()
    {

        $this->_init('\FME\Mediaappearance\Model\Mediaappearance', '\FME\Mediaappearance\Model\ResourceModel\Mediaappearance');
        $this->_map['fields']['mediagallery_id'] = 'main_table.mediagallery_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    } 

    /**
     * addStoreFilter
     * @param $store
     */
//    public function addStoreFilter($store) {
//        if ($store instanceof \Magento\Store\Model\Store) {
//            $store = array($store->getId());
//        }
//
//        $this->getSelect()
//                ->join(
//                        array('store_table' => $this->getTable('fme_media_store')), 'main_table.mediagallery_id = store_table.mediagallery_id', array()
//                )
//                ->where('store_table.store_id in (?)', array(0, $store))
//                ->group('main_table.mediagallery_id');
//
//        return $this;
//    }
    
//    public function addStoreFilter($store, $withAdmin = true) {
//        
//        if ($store instanceof \Magento\Store\Model\Store) {
//            
//            $store = array($store->getId());
//        }
//
//        $this->getSelect()
//                ->join(
//                        array('store_table' => $this->getTable('fme_media_store')),
//                        'main_table.mediagallery_id = store_table.mediagallery_id', array()
//                )
//                ->where('store_table.store_id in (?)', array(0, $store))
//                ->group('main_table.mediagallery_id');
//
//        return $this;
//    }
    
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof \Magento\Store\Model\Store) {
                $store = [$store->getId()];
            }

            if (!is_array($store)) {
                $store = [$store];
            }

            if ($withAdmin) {
                $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }

            $this->addFilter('store', ['in' => $store], 'public');
        }
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        
        $items = $this->getColumnValues('mediagallery_id');
        if (count($items)) {
            $connection = $this->getConnection();
            //Store Selection
            $select = $connection->select()
                    
                    ->from(['cps' => $this->getTable('fme_media_store')], ['mediagallery_id', 'store_id'])
                    ->where('cps.mediagallery_id IN (?)', $items);
            
            $result = $connection->fetchPairs($select);
            
            if ($result) {
                foreach ($this as $item) {
                    $pageId = $item->getData('mediagallery_id');
                    if (!isset($result[$pageId])) {
                        continue;
                    }
                    if ($result[$pageId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = $result[$item->getData('mediagallery_id')];
                        $storeCode = $this->_storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', [$result[$pageId]]);
                }
            }
            //Store Selection End
            


        }
        $items = $this->getColumnValues('mediagallery_id');

        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['cps' => $this->getTable('fme_mediagallery_cmspage')], ['id','cms_id'])
                    ->where('cps.mediagallery_id IN (?)', $items);
            $result = $connection->fetchPairs($select);
               
            if ($result) {
                $cms_idd = implode(',', $result);

                $item->setData('cmspage_ids', $cms_idd);
            }
        }


        $items = $this->getColumnValues('mediagallery_id');

        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['cps' => $this->getTable('fme_mediagallery_category')], ['id','category_id'])
                    ->where('cps.mediagallery_id IN (?)', $items);
            $result = $connection->fetchPairs($select);
               
            if ($result) {
                $cms_idd = implode(',', $result);
                $item->setData('category_ids', $cms_idd);
            
               // $item->setData('mediagallery_categories', $cms_idd);
            }
        }
        $this->_previewFlag = false;
        return parent::_afterLoad();
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()
            ->join(
                ['store_table' => $this->getTable('fme_media_store')],
                'main_table.mediagallery_id = store_table.mediagallery_id',
                []
            )
                    ->group('main_table.mediagallery_id');
        }
        parent::_renderFiltersBefore();
    }
}
