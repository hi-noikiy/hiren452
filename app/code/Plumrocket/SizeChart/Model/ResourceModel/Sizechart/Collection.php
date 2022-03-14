<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_SizeChart
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\SizeChart\Model\ResourceModel\Sizechart;

use Plumrocket\SizeChart\Model\ResourceModel\Sizechart as SizechartResourceModel;
use Plumrocket\SizeChart\Model\Sizechart as SizechartModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(SizechartModel::class, SizechartResourceModel::class);
    }

    public function addEnabledFilter()
    {
        return $this->addFieldToFilter('status', 1);
    }

    public function addStoreFilter($storeIds)
    {
        if ($storeIds) {
            if (!is_array($storeIds)) {
                $storeIds = [$storeIds];
            }
            if (!in_array(0, $storeIds)) {
                $storeIds[] = 0;
            }

            $filterParams = [];

            foreach ($storeIds as $storeId) {
                $filterParams[]['finset'] = $storeId;
            }

            $this->addFieldToFilter('store_id', $filterParams);
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
        parent::_afterLoad();

        foreach ($this as $item) {
            if ($item->getStoreId() && $item->getStoreId() != '0') {
                $item->setStoreId(explode(',', $item->getStoreId()));
            } else {
                $item->setStoreId(['0']);
            }
        }
        return $this;
    }
}
