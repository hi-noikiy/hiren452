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

namespace Magezon\ProductPagePdf\Model;

class Processor extends \Magezon\Core\Model\ConditionsProcessor
{
    public function process(\Magezon\ProductPagePdf\Model\Profile $profiles)
    {
       if ($this->_storeManager->isSingleStoreMode()) {
            $store = $this->_storeManager->getStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
            $this->__processBystore($profiles, $store);
        } else {
           $storeIds = (array) $profiles->getData('store_id');
           if (!empty($storeIds)) {
               if (in_array(0, $storeIds)) {
                   $stores = $this->_systemStore->getStoreValuesForForm();
                   foreach ($stores as $store) {
                       if (is_array($store['value']) && !empty($store['value'])) {
                           foreach ($store['value'] as $_store) {
                               $store = $this->_storeManager->getStore($_store['value']);
                               $this->__processBystore($profiles, $store);
                           }
                       }
                   }
               } else {
                   foreach ($storeIds as $storeId) {
                       $store = $this->_storeManager->getStore($storeId);
                       $this->__processBystore($profiles, $store);
                   }
               }
           }
        }
    }

    public function __processBystore($profiles, $store)
    {
        $collection = $this->getProductByConditions($profiles, $store);
        $this->_saveTabRuleProduct($profiles, $collection, $store);
    }

    protected function _saveTabRuleProduct($profiles, $collection, $store)
    {
        $profilesId = $profiles->getId();
        $table = $this->_resource->getTableName('mgz_productpagepdf_profile_product');
        $connection = $this->_resource->getConnection();
        $newRecords = [];
        $items = $collection;
        foreach ($items as $_item) {
            $newRecords[] = $_item->getId();
        }
        $where = ['profile_id = ?' => $profilesId, 'store_id = ?' => $store->getId()];
        $connection->delete($table, $where);
        if ($newRecords) {
            $data = [];
            foreach ($newRecords as $productId) {
                $data[] = [
                    'profile_id' => $profilesId,
                    'product_id' => $productId,
                    'store_id'   => $store->getId()
                ];
            }

            if ($this->_storeManager->isSingleStoreMode()) {
                $storeId = $this->_storeManager->getDefaultStoreView()->getId();
                $where = ['profile_id = ?' => $profilesId, 'store_id = ?' => $storeId];
                $connection->delete($table, $where);
                foreach ($newRecords as $productId) {
                    $data[] = [
                        'profile_id'   => $profilesId,
                        'product_id' => $productId,
                        'store_id'   => $storeId
                    ];
                }
            }
            $connection->insertMultiple($table, $data);
        }
    }
}
