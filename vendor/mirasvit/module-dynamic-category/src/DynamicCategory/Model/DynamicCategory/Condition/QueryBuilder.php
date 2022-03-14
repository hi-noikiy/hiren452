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

namespace Mirasvit\DynamicCategory\Model\DynamicCategory\Condition;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\Module\Manager as ModuleManager;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\DynamicCategory\Registry;
use Mirasvit\DynamicCategory\Service\CategoryService;

class QueryBuilder
{
    const STATIC_FIELDS = ['entity_id', 'sku', 'attribute_set_id', 'type_id', 'created_at', 'updated_at'];

    private $salt = 0;

    private $categoryRepository;

    private $categoryService;

    private $resource;

    private $connection;

    private $eavConfig;


    private $moduleManager;

    private $registry;

    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryService $categoryService,
        Registry $registry,
        ModuleManager $moduleManager,
        ResourceConnection $resource,
        EavConfig $eavConfig
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryService    = $categoryService;
        $this->resource           = $resource;
        $this->connection         = $resource->getConnection();
        $this->eavConfig          = $eavConfig;
        $this->moduleManager      = $moduleManager;
        $this->registry           = $registry;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function buildCondition(Select $select, string $fieldName, string $operator, string $value): string
    {
        $fieldCondition = $this->joinField($select, $fieldName);

        if (!$fieldCondition) {
            return '';
        }

        if (in_array($operator, ['()', '!()', '{}', '!{}'])) {
            $value = array_filter(explode(',', $value));
        }

        switch ($operator) {
            case '==':
                return $this->conditionEQ($fieldCondition, $value);

            case '!=':
                return $this->conditionNEQ($fieldCondition, $value);

            case '()':
                return $this->conditionIsOneOf($fieldCondition, $value);

            case '!()':
                return $this->conditionNotIsOneOf($fieldCondition, $value);

            case '<=>':
                return $this->conditionIsUndefined($fieldCondition);

            case '>':
                return $this->conditionGt($fieldCondition, $value);

            case '>=':
                return $this->conditionGtEq($fieldCondition, $value);

            case '<':
                return $this->conditionLt($fieldCondition, $value);

            case '<=':
                return $this->conditionLtEq($fieldCondition, $value);

            case '{}':
                return $this->conditionContains($fieldCondition, $value);

            case '!{}':
                return $this->conditionDoesNotContain($fieldCondition, $value);

            default:
                return '';
        }
    }

    public function joinField(Select $select, string $fieldName): string
    {
        if (in_array($fieldName, self::STATIC_FIELDS)) {
            $fieldCondition = "e.{$fieldName}";

            $select->columns([
                $fieldName => $fieldCondition,
            ]);

            return $fieldCondition;
        }

        $attribute = $this->eavConfig->getAttribute('catalog_product', $fieldName);

        if (!$attribute->getId()) {
            return '';
        }

        $salt = '_' . $this->salt++;
        $code = $attribute->getAttributeCode();

        if ($code == 'category_ids') {
            $table      = $this->resource->getTableName('catalog_category_product');
            $tableAlias = "tbl_{$code}{$salt}";
            $field      = "{$tableAlias}.category_id";
            $select->joinLeft(
                [$tableAlias => $table],
                "e.entity_id = {$tableAlias}.product_id",
                [$code = $field]
            )->group('e.entity_id');

            return $field;
        }

        if ($code == 'quantity_and_stock_status') {
            return $this->joinStockStatus($select);
        }

        $table = $attribute->getBackendTable();

        $tableAlias = "tbl_{$code}{$salt}";

        $field = "{$tableAlias}.value";

        if (!$this->isJoined($select, $field)) {
            if (CompatibilityService::isEnterprise()) {
                $condition = "e.row_id = {$tableAlias}.row_id AND {$tableAlias}.attribute_id = {$attribute->getId()}";
            } else {
                $condition = "e.entity_id = {$tableAlias}.entity_id AND {$tableAlias}.attribute_id = {$attribute->getId()}";
            }

            $select->joinLeft(
                [$tableAlias => $table],
                $condition,
                [$code => $field]
            );
        }

        return $field;
    }

    private function joinStockStatus(Select $select): string
    {
        if ($this->moduleManager->isEnabled('Magento_Inventory')) {
            $result = $this->getInventoryStock($select);
        } else {
            $result = $this->getDefaultStock($select);
        }

        return $result;
    }

    private function getDefaultStock(Select $select): string
    {
        $select->joinInner(
            ['stock' => $this->resource->getTableName('cataloginventory_stock_status')],
            'stock.product_id = e.entity_id',
            ['quantity_and_stock_status' => 'stock_status']
        );

        return 'stock.quantity_and_stock_status';
    }

    private function getInventoryStock(Select $select): string
    {
        $connection = $this->resource->getConnection();

        $category = $this->registry->getCategory();
        if (!$category) {
            $dynamicCategory = $this->registry->getCurrentDynamicCategory();
            $category = $this->categoryRepository->get($dynamicCategory->getCategoryId());
        }

        $path = explode('/', $category->getPath());
        $rootCategoryIds = $this->categoryService->getRootCategoryIds();

        $storeId = 0;
        foreach ($rootCategoryIds as $rootCategoryId => $id) {
            if (in_array($rootCategoryId, $path)) {
                $storeId = $id;
                break;
            }
        }

        $stockSelect = $connection->select();
        $stockSelect->from(
            ['store' => $this->resource->getTableName('store')],
            ['website_id', 'store_id']
        )->joinInner(
            ['store_website' => $this->resource->getTableName('store_website')],
            'store.website_id = store_website.website_id',
            null
        )->joinInner(
            ['stock' => $this->resource->getTableName('inventory_stock_sales_channel')],
            'store_website.code = stock.code',
            null
        )->joinInner(
            ['source_link' => $this->resource->getTableName('inventory_source_stock_link')],
            'stock.stock_id = source_link.stock_id',
            ['stock_id']
        )->where('store.store_id = ' . $storeId);

        $stmt = $connection->query($stockSelect);

        $stockId = $stmt->fetchObject()->stock_id;

        if ($stockId == 1) {
            $select->joinInner(
                ['stock' => $this->resource->getTableName('cataloginventory_stock_status')],
                'stock.product_id = e.entity_id',
                ['quantity_and_stock_status' => 'stock_status']
            );
        } else {
            $select->joinInner(
                ['stock' => $this->resource->getTableName("inventory_stock_$stockId")],
                'e.sku = stock.sku',
                ['quantity_and_stock_status' => 'is_salable']
            );
        }

        return 'stock.stock_status';
    }

    public function getResource(): ResourceConnection
    {
        return $this->resource;
    }

    private function conditionEQ(string $field, string $value): string
    {
        return $this->connection->quoteInto("${field} = ?", $value);
    }

    private function conditionNEQ(string $field, string $value): string
    {
        return $this->connection->quoteInto("${field} NOT IN (?)", $value);
    }

    private function conditionGt(string $field, string $value): string
    {
        return $this->connection->quoteInto("${field} > ?", $value);
    }

    private function conditionGtEq(string $field, string $value): string
    {
        return $this->connection->quoteInto("${field} >= ?", $value);
    }

    private function conditionLt(string $field, string $value): string
    {
        return $this->connection->quoteInto("${field} < ?", $value);
    }

    private function conditionLtEq(string $field, string $value): string
    {
        return $this->connection->quoteInto("${field} <= ?", $value);
    }

    private function conditionIsOneOf(string $field, array $value): string
    {
        $parts = [];
        foreach ($value as $v) {
            $parts[] = $this->connection->quoteInto("FIND_IN_SET(?, {$field})", $v);
        }

        return implode(' OR ', $parts);
    }

    private function conditionContains(string $field, array $value): string
    {
        $parts = [];
        foreach ($value as $v) {
            $parts[] = $this->connection->quoteInto("{$field} LIKE ?", '%' . $v . '%');
        }

        return implode(' OR ', $parts);
    }

    private function conditionDoesNotContain(string $field, array $value)
    {
        $parts = [];
        foreach ($value as $v) {
            $parts[] = $this->connection->quoteInto("{$field} NOT LIKE ?", '%' . $v . '%');
        }

        return implode(' AND ', $parts);
    }

    private function conditionNotIsOneOf(string $field, array $value): string
    {
        $parts = [];
        foreach ($value as $v) {
            $parts[] = $this->connection->quoteInto("FIND_IN_SET(?, {$field}) = 0", $v);
        }

        return implode(' AND ', $parts);
    }

    private function conditionIsUndefined(string $field): string
    {
        $parts = [
            "{$field} IS NULL",
            "{$field} = ''",
        ];

        return implode(' OR ', $parts);
    }

    private function isJoined(Select $select, string $field): bool
    {
        return strpos((string)$select, $field) !== false;
    }
}
