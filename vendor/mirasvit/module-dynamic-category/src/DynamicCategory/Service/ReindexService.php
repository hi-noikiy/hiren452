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
 * @package   mirasvit/module-dynamic-category
 * @version   1.0.17
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\DynamicCategory\Service;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Query\BatchIteratorInterface;
use Magento\Framework\DB\Query\Generator as QueryGenerator;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Mirasvit\DynamicCategory\Registry;
use Mirasvit\DynamicCategory\Api\Data\DynamicCategoryInterface;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReindexService
{
    const FIELD_CATEGORY_ID = 'category_id';
    const FIELD_PRODUCT_ID  = 'product_id';
    const FIELD_POSITION    = 'position';

    private $productCollectionFactory;

    private $dynamicCategoryRepository;

    private $categoryRepository;

    private $metadataPool;

    private $queryGenerator;

    private $connection;

    private $registry;

    private $resource;

    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        DynamicCategoryRepository $dynamicCategoryRepository,
        Registry $registry,
        CategoryRepository $categoryRepository,
        MetadataPool $metadataPool,
        QueryGenerator $queryGenerator,
        ResourceConnection $resource
    ) {
        $this->productCollectionFactory  = $productCollectionFactory;
        $this->dynamicCategoryRepository = $dynamicCategoryRepository;
        $this->categoryRepository        = $categoryRepository;
        $this->metadataPool              = $metadataPool;
        $this->queryGenerator            = $queryGenerator;
        $this->registry                  = $registry;
        $this->resource                  = $resource;
        $this->connection                = $resource->getConnection();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function reindexCategory(DynamicCategoryInterface $dynamicCategory): void
    {
        $entityMetadata = $this->metadataPool->getMetadata(ProductInterface::class);

        try {
            $category = $this->categoryRepository->get($dynamicCategory->getCategoryId());
        } catch (\Exception $e) {
            // category was removed
            $this->dynamicCategoryRepository->delete($dynamicCategory);

            return;
        }

        $this->registry->setCategory($category);

        $indexSelect = $this->getIndexedProductsForCategoryQuery($dynamicCategory->getCategoryId());

        $page = 0;

        $productCollection = $this->productCollectionFactory->create();

        $productCollection->getSelect()->limit(5000, 5000 * $page);

        while ($productCollection->count() > 0) {
            $c = clone $productCollection;

            $dynamicCategory->getRule()->applyToFullCollection($c);

            if ($page === 0) {
                $this->removeProducts($c, $indexSelect);
            }

            $resultSelect = $c->getSelect();

            $resultSelect->where('e.' . $entityMetadata->getIdentifierField() . ' NOT IN(' . $indexSelect->__toString() . ')');

            $resultSelect->reset($resultSelect::COLUMNS);
            $resultSelect->reset($resultSelect::LIMIT_COUNT);
            $resultSelect->reset($resultSelect::LIMIT_OFFSET);

            $resultSelect->columns([
                self::FIELD_CATEGORY_ID => new \Zend_Db_Expr($dynamicCategory->getCategoryId()),
                self::FIELD_PRODUCT_ID  => 'e.entity_id',
                self::FIELD_POSITION    => new \Zend_Db_Expr(0),
            ]);

            $countSelect = clone $resultSelect;
            $countSelect->reset($countSelect::COLUMNS);
            $countSelect->reset($countSelect::LIMIT_COUNT);
            $countSelect->reset($countSelect::LIMIT_OFFSET);
            $countSelect->columns([
                new \Zend_Db_Expr('COUNT(*)'),
            ]);

            $total = $countSelect->query()->fetchColumn();
            if ($total > 0) {
                $this->connection->query(
                    $this->connection->insertFromSelect(
                        $resultSelect,
                        $this->resource->getTableName('catalog_category_product'),
                        [self::FIELD_CATEGORY_ID, self::FIELD_PRODUCT_ID, self::FIELD_POSITION],
                        AdapterInterface::INSERT_ON_DUPLICATE
                    )
                );
            }

            $page++;

            $productCollection->clear()->getSelect()->limit(5000, 5000 * $page);
        }

        $this->registry->setCategory(null);
    }

    /**
     * @see \Magento\Catalog\Model\Indexer\Category\Product\AbstractAction::prepareSelectsByRange()
     */
    public function prepareSelectsByRange(Select $select, string $field, int $range = 5000): array
    {
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

    private function getIndexedProductsForCategoryQuery(int $id): Select
    {
        $indexSelect = $this->connection->select();
        $indexSelect->from($this->resource->getTableName('catalog_category_product'), self::FIELD_PRODUCT_ID);
        $indexSelect->where(self::FIELD_CATEGORY_ID . ' = ' . $id);

        return $indexSelect;
    }

    private function removeProducts(Collection $productCollection, Select $indexSelect)
    {
        $collection            = clone $productCollection;
        $productCategorySelect = clone $indexSelect;

        $productCategorySelect->reset($productCategorySelect::COLUMNS);
        $productCategorySelect->reset($productCategorySelect::LIMIT_COUNT);
        $productCategorySelect->reset($productCategorySelect::LIMIT_OFFSET);
        $productCategorySelect->columns('entity_id');

        $resultSelect = $collection->getSelect();

        $resultSelect->reset($resultSelect::COLUMNS);
        $resultSelect->reset($resultSelect::LIMIT_COUNT);
        $resultSelect->reset($resultSelect::LIMIT_OFFSET);

        $resultSelect->columns([
            self::FIELD_PRODUCT_ID => 'e.entity_id',
        ]);

        $productCategorySelect->where('product_id' . ' NOT IN(' . $resultSelect->__toString() . ')');


        $batchQueries = $this->prepareSelectsByRange(
            $productCategorySelect,
            'entity_id',
            5000
        );

        foreach ($batchQueries as $query) {
            $result = $this->connection->query(
                $query
            );

            $ids = [];
            foreach ($result->fetchAll() as $row) {
                $ids[] = $row['entity_id'];
            }

            if ($ids) {
                $this->connection->delete(
                    $this->resource->getTableName('catalog_category_product'),
                    'entity_id in (' . implode(',', $ids) . ')'
                );
            }
        }
    }
}
