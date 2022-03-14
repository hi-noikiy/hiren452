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
 * @package   mirasvit/module-product-action
 * @version   1.0.9
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\ProductAction\Action\UpdateCategory;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Query\BatchIteratorInterface;
use Magento\Framework\DB\Query\Generator as QueryGenerator;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Indexer\Product\Price\Processor;
use Mirasvit\ProductAction\Api\ActionDataInterface;
use Mirasvit\ProductAction\Api\ExecutorInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Executor implements ExecutorInterface
{
    const FIELD_CATEGORY_ID = 'category_id';
    const FIELD_PRODUCT_ID  = 'product_id';
    const FIELD_POSITION    = 'position';

    private $collectionFactory;

    private $connection;

    private $metadataPool;

    private $productPriceIndexerProcessor;

    private $queryGenerator;

    private $resource;

    private $actionDataFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        MetadataPool $metadataPool,
        Processor $productPriceIndexerProcessor,
        QueryGenerator $queryGenerator,
        ResourceConnection $resource,
        ActionDataFactory $actionDataFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->metadataPool      = $metadataPool;
        $this->queryGenerator    = $queryGenerator;

        $this->productPriceIndexerProcessor = $productPriceIndexerProcessor;

        $this->resource   = $resource;
        $this->connection = $resource->getConnection();

        $this->actionDataFactory = $actionDataFactory;
    }

    /**
     * @param ActionDataInterface $actionData
     */
    public function execute(ActionDataInterface $actionData): void
    {
        $actionCategoryData = $this->cast($actionData);

        if ($actionCategoryData->getAddCategoryIds()) {
            $this->addCategory($actionCategoryData->getIds(), $actionCategoryData->getAddCategoryIds());
        }

        if ($actionCategoryData->getRemoveCategoryIds()) {
            $this->removeCategory($actionCategoryData->getIds(), $actionCategoryData->getRemoveCategoryIds());
        }

        $this->productPriceIndexerProcessor->reindexList($actionCategoryData->getIds());
    }

    private function addCategory(array $ids, array $categoryIds): void
    {
        foreach ($categoryIds as $categoryId) {
            $entityMetadata = $this->metadataPool->getMetadata(ProductInterface::class);

            $resultSelect = $this->getProductSelect($ids);

            $resultSelect->reset($resultSelect::COLUMNS);
            $resultSelect->columns([
                self::FIELD_CATEGORY_ID => new \Zend_Db_Expr($categoryId),
                self::FIELD_PRODUCT_ID  => 'e.' . $entityMetadata->getIdentifierField(),
                self::FIELD_POSITION    => new \Zend_Db_Expr(0),
            ]);

            $batchQueries = $this->prepareSelectsByRange(
                $resultSelect,
                $entityMetadata->getIdentifierField(),
                self::PRODUCTS_PER_INSERT
            );

            foreach ($batchQueries as $query) {
                $this->connection->query(
                    $this->connection->insertFromSelect(
                        $query,
                        $this->resource->getTableName('catalog_category_product'),
                        [self::FIELD_CATEGORY_ID, self::FIELD_PRODUCT_ID, self::FIELD_POSITION],
                        AdapterInterface::INSERT_ON_DUPLICATE
                    )
                );
            }
        }
    }

    private function removeCategory(array $ids, array $categoryIds): void
    {
        $parts = array_chunk($ids, self::PRODUCTS_PER_INSERT);
        foreach ($parts as $part) {
            $this->connection->delete(
                $this->resource->getTableName('catalog_category_product'),
                self::FIELD_PRODUCT_ID . ' IN (' . implode(',', $part) . ') AND ' .
                self::FIELD_CATEGORY_ID . ' IN (' . implode(',', $categoryIds) . ')'
            );
        }
    }

    private function getProductSelect(array $ids): Select
    {
        $entityMetadata = $this->metadataPool->getMetadata(ProductInterface::class);

        $productCollection = $this->collectionFactory->create();
        $productCollection->addFieldToFilter($entityMetadata->getIdentifierField(), ['in' => $ids]);

        return $productCollection->getSelect();
    }

    /**
     * @see \Magento\Catalog\Model\Indexer\Category\Product\AbstractAction::prepareSelectsByRange()
     */
    private function prepareSelectsByRange(
        Select $select,
        string $field,
        int $range = 500
    ): array {
        $iterator = $this->queryGenerator->generate(
            $field,
            $select,
            $range,
            BatchIteratorInterface::NON_UNIQUE_FIELD_ITERATOR
        );

        $queries = [];
        foreach ($iterator as $query) {
            $queries[] = $query;
        }

        return $queries;
    }

    private function cast(ActionDataInterface $class): ActionData
    {
        if ($class instanceof ActionData) {
            return $class;
        }

        throw new \InvalidArgumentException((string)__('Invalid class'));
    }
}
