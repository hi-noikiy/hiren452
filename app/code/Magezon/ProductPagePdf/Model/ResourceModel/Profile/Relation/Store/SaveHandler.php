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

namespace Magezon\ProductPagePdf\Model\ResourceModel\Profile\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magezon\ProductPagePdf\Api\Data\ProfileInterface;
use Magezon\ProductPagePdf\Model\ResourceModel\Profile;

class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Profile
     */
    protected $resourceProfile;

    /**
     * @param MetadataPool $metadataPool
     * @param Profile $resourceProfile
     */
    public function __construct(
        MetadataPool $metadataPool,
        Profile $resourceProfile
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceProfile = $resourceProfile;
    }

    /**
     * @param \Magezon\ProductPagePdf\Model\Profile $entity
     * @param array $arguments
     * @return \Magezon\ProductPagePdf\Model\Profile
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->hasData('store_id')) {
            $entityMetadata = $this->metadataPool->getMetadata(ProfileInterface::class);
            $linkField = $entityMetadata->getLinkField();
            $connection = $entityMetadata->getEntityConnection();
            $newStores = (array)$entity->getStoreId();
            $table = $this->resourceProfile->getTable('mgz_productpagepdf_profile_store');
            $where = [
                'profile_id = ?' => (int)$entity->getData($linkField)
            ];
            $connection->delete($table, $where);

            $data = [];
            foreach ($newStores as $k => $storeId) {
                $data[] = [
                    'profile_id' => (int)$entity->getData($linkField),
                    'store_id' => (int)$storeId
                ];
            }
            if (!empty($data)) {
                $connection->insertMultiple($table, $data);
            }
        }
        return $entity;
    }
}
