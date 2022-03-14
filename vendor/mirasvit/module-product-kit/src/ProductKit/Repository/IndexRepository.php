<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Repository;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\ProductKit\Api\Data\IndexInterface;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;

class IndexRepository
{
    private $config;

    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var array
     */
    private $rowPool;

    public function __construct(
        Config $config,
        ResourceConnection $resource
    ) {
        $this->config     = $config;
        $this->resource   = $resource;
        $this->connection = $resource->getConnection();
    }

    public function delete(KitInterface $kit)
    {
        $this->connection->delete(
            $this->resource->getTableName(IndexInterface::TABLE_NAME),
            [IndexInterface::KIT_ID . ' = ' . $kit->getId()]
        );

        return true;
    }

    public function insertRow(KitInterface $kit, KitItemInterface $item)
    {
        $this->rowPool[] = [
            IndexInterface::KIT_ID      => $kit->getId(),
            IndexInterface::ITEM_ID     => $item->getId(),
            IndexInterface::PRODUCT_ID  => $item->getProductId(),
            IndexInterface::POSITION    => $item->getPosition(),
            IndexInterface::IS_OPTIONAL => $item->isOptional(),
            IndexInterface::IS_PRIMARY  => $item->isPrimary(),
        ];

        if (count($this->rowPool) > 1000) {
            $this->push();
        }

        return true;
    }

    public function commit()
    {
        $this->push();
    }

    public function select()
    {
        return $this->resource->getConnection()->select()
            ->from(['index' => $this->resource->getTableName(IndexInterface::TABLE_NAME)], [])
            ->joinLeft(
                ['kit' => $this->resource->getTableName(KitInterface::TABLE_NAME)],
                'kit.kit_id = index.kit_id',
                []
            );
    }

    /**
     * @param int|null $storeId
     *
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function selectWithProductVisibility($storeId = null)
    {
        $visibilityAttributeId = $this->config->getAttribute(
            Product::ENTITY,
            'visibility'
        )->getId();

        $statusAttributeId = $this->config->getAttribute(
            Product::ENTITY,
            'status'
        )->getId();

        $storeIds = [0, (int)$storeId];

        $magentoVersion = CompatibilityService::getVersion();
        if (version_compare($magentoVersion, '2.3.5', '>=') && CompatibilityService::isEnterprise()) {
            return $this->select()->joinInner(
                ['cpvd' => $this->resource->getTableName('catalog_product_entity_int')],
                'cpvd.row_id = index.product_id AND cpvd.store_id IN (' . implode(',', $storeIds) . ')' .
                ' AND cpvd.attribute_id = ' . $visibilityAttributeId,
                []
            )->joinInner(
                ['cpvd2' => $this->resource->getTableName('catalog_product_entity_int')],
                'cpvd2.row_id = index.product_id AND cpvd2.store_id = 0' .
                ' AND cpvd2.attribute_id = ' . $statusAttributeId .
                ' AND cpvd2.value <> 2',
                []
            );
        } else {
            return $this->select()->joinInner(
                ['cpvd' => $this->resource->getTableName('catalog_product_entity_int')],
                'cpvd.entity_id = index.product_id AND cpvd.store_id IN (' . implode(',', $storeIds) . ')' .
                ' AND cpvd.attribute_id = ' . $visibilityAttributeId,
                []
            )->joinInner(
                ['cpvd2' => $this->resource->getTableName('catalog_product_entity_int')],
                'cpvd2.entity_id = index.product_id AND cpvd2.store_id = 0' .
                ' AND cpvd2.attribute_id = ' . $statusAttributeId .
                ' AND cpvd2.value <> 2',
                []
            );
        }
    }

    private function push()
    {
        if (!$this->rowPool) {
            return;
        }

        $this->connection->insertMultiple(
            $this->resource->getTableName(IndexInterface::TABLE_NAME),
            $this->rowPool
        );

        $this->rowPool = [];
    }
}
