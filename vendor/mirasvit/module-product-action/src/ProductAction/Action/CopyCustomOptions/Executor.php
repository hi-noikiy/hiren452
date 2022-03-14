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

namespace Mirasvit\ProductAction\Action\CopyCustomOptions;

use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option\Repository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Mirasvit\ProductAction\Api\ActionDataInterface;
use Mirasvit\ProductAction\Api\ExecutorInterface;

class Executor implements ExecutorInterface
{
    private $dataObjectHelper;

    private $dataPersistor;

    private $productRepository;

    private $repository;

    private $resourceConnection;

    public function __construct(
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        ProductRepositoryInterface $productRepository,
        Repository $repository,
        ResourceConnection $resourceConnection
    ) {
        $this->dataObjectHelper   = $dataObjectHelper;
        $this->dataPersistor      = $dataPersistor;
        $this->productRepository  = $productRepository;
        $this->repository         = $repository;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(ActionDataInterface $actionData): void
    {
        $actionData = $this->cast($actionData);

        try {
            $this->copyOption($actionData);
        } catch (\Exception $e) {
            echo '<pre>';
            var_dump($e->getMessage());
            echo($e->getFile());
            echo($e->getLine());
            die;
        }
    }

    private function cast(ActionDataInterface $class): ActionData
    {
        if ($class instanceof ActionData) {
            return $class;
        }

        throw new \InvalidArgumentException((string)__('Invalid class'));
    }

    private function copyOption(ActionData $actionData): void
    {
        $ids              = $actionData->getIds();
        $copyFromSkus     = $actionData->getCopyFrom();
        $isReplaceOptions = $actionData->getIsReplaceOptions();

        foreach ($copyFromSkus as $sku) {
            $product = $this->productRepository->get($sku, true);

            if (!$isReplaceOptions) {
                foreach ($ids as $id) {
                    /** @var Product $p */
                    $p = $this->productRepository->getById($id, true);

                    $this->repository->duplicate($product, $p);
                }
            } else {
                $fromOptions = $product->getOptions();

                foreach ($ids as $id) {
                    /** @var Product $p */
                    $p = $this->productRepository->getById($id, true);

                    $toOptions = $p->getOptions();

                    foreach ($fromOptions as $fromOption) {
                        $isOptionExists = false;

                        foreach ($toOptions as $toOption) {
                            if ($fromOption->getTitle() == $toOption->getTitle()) {
                                $isOptionExists = true;

                                $this->updateOption($fromOption, $toOption);
                            }
                        }

                        if (!$isOptionExists) {
                            $this->addOption($fromOption, (int)$p->getId());
                        }
                    }
                }
            }
        }

        $this->dataPersistor->clear('catalog_product');
    }

    /**
     * @see \Magento\Catalog\Model\ResourceModel\Product\Option::duplicate()
     */
    public function addOption(ProductCustomOptionInterface $fromOption, int $newProductId): void
    {
        $fromOptionId = (int)$fromOption->getOptionId();

        $connection = $this->resourceConnection->getConnection();
        $mainTable  = $this->resourceConnection->getTableName('catalog_product_option');

        // read and prepare original product options
        $select = $connection->select()->from(
            $this->resourceConnection->getTableName('catalog_product_option')
        )->where(
            'option_id = ?',
            $fromOptionId
        );

        $row = $connection->query($select)->fetch();

        unset($row['option_id']);
        $row['product_id'] = $newProductId;

        $connection->insert($mainTable, $row);

        $toOptionId = (int)$connection->lastInsertId($mainTable);

        // title
        $table = $this->resourceConnection->getTableName('catalog_product_option_title');

        $select = $this->resourceConnection->getConnection()->select()->from(
            $table,
            [new \Zend_Db_Expr($toOptionId), 'store_id', 'title']
        )->where(
            'option_id = ?',
            $fromOptionId
        );

        $insertSelect = $connection->insertFromSelect(
            $select,
            $table,
            ['option_id', 'store_id', 'title'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
        );
        $connection->query($insertSelect);

        // price
        $table = $this->resourceConnection->getTableName('catalog_product_option_price');

        $select = $connection->select()->from(
            $table,
            [new \Zend_Db_Expr($toOptionId), 'store_id', 'price', 'price_type']
        )->where(
            'option_id = ?',
            $fromOptionId
        );

        $insertSelect = $connection->insertFromSelect(
            $select,
            $table,
            ['option_id', 'store_id', 'price', 'price_type'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
        );
        $connection->query($insertSelect);

        $this->updateValue($fromOptionId, $toOptionId);
    }

    public function updateOption(ProductCustomOptionInterface $fromOption, ProductCustomOptionInterface $toOption): void
    {
        $fromOptionId = (int)$fromOption->getOptionId();
        $toOptionId   = (int)$toOption->getOptionId();

        $connection = $this->resourceConnection->getConnection();
        $mainTable  = $this->resourceConnection->getTableName('catalog_product_option');

        // read and prepare original product options
        $select = $connection->select()->from(
            $this->resourceConnection->getTableName('catalog_product_option')
        )->where(
            'option_id = ?',
            $fromOptionId
        );

        $row = $connection->query($select)->fetch();

        unset($row['option_id']);

        $row['product_id'] = $toOption->getProductId();

        $connection->update($mainTable, $row, 'option_id = ' . $toOptionId);

        // copy options prefs
        // title
        $table = $this->resourceConnection->getTableName('catalog_product_option_title');

        $select = $this->resourceConnection->getConnection()->select()->from(
            $table,
            ['title']
        )->where(
            'option_id = ?',
            $fromOptionId
        );

        $row = $connection->query($select)->fetch();

        unset($row['option_title_id']);

        $connection->update($table, $row, 'option_id = ' . $toOptionId);

        // price
        $table = $this->resourceConnection->getTableName('catalog_product_option_price');

        $select = $connection->select()->from(
            $table,
            ['price', 'price_type']
        )->where(
            'option_id = ?',
            $fromOptionId
        );

        $row = $connection->query($select)->fetch();

        unset($row['option_price_id']);

        $connection->update($table, $row, 'option_id = ' . $toOptionId);

        $this->updateValue($fromOptionId, $toOptionId);
    }

    private function updateValue(int $fromOptionId, int $toOptionId): void
    {
        $connection = $this->resourceConnection->getConnection();
        $mainTable  = $this->resourceConnection->getTableName('catalog_product_option_type_value');

        // remove old values
        $connection->delete($mainTable, 'option_id = ' . $toOptionId);

        $select    = $connection->select()->from($mainTable)->where('option_id = ?', $fromOptionId);
        $valueData = $connection->fetchAll($select);

        $valueCond = [];

        foreach ($valueData as $data) {
            $optionTypeId = $data['option_type_id'];

            unset($data['option_type_id']);

            $data['option_id'] = $toOptionId;

            $connection->insert($mainTable, $data);

            $valueCond[$optionTypeId] = $connection->lastInsertId($mainTable);
        }

        unset($valueData);

        foreach ($valueCond as $oldTypeId => $newTypeId) {
            // price
            $priceTable = $this->resourceConnection->getTableName('catalog_product_option_type_price');
            $columns    = [new \Zend_Db_Expr($newTypeId), 'store_id', 'price', 'price_type'];

            $select = $connection->select()->from(
                $priceTable,
                []
            )->where(
                'option_type_id = ?',
                $oldTypeId
            )->columns(
                $columns
            );

            $insertSelect = $connection->insertFromSelect(
                $select,
                $priceTable,
                ['option_type_id', 'store_id', 'price', 'price_type']
            );

            $connection->query($insertSelect);

            // title
            $titleTable = $this->resourceConnection->getTableName('catalog_product_option_type_title');
            $columns = [new \Zend_Db_Expr($newTypeId), 'store_id', 'title'];

            $select = $this->resourceConnection->getConnection()->select()->from(
                $titleTable,
                []
            )->where(
                'option_type_id = ?',
                $oldTypeId
            )->columns(
                $columns
            );

            $insertSelect = $connection->insertFromSelect(
                $select,
                $titleTable,
                ['option_type_id', 'store_id', 'title']
            );

            $connection->query($insertSelect);
        }
    }
}
