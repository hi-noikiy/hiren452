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



namespace Mirasvit\ProductKit\Service\Product;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Model\Stock\ItemFactory as StockItemFactory;
use Magento\Catalog\Model\Product;
use Magento\Bundle\Model\Product\Type as ProductTypeBundle;
use Magento\GroupedProduct\Model\Product\Type\Grouped as ProductTypeGrouped;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory as ProductTypeConfigurableFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Module\Manager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CheckQtyService
{
    private static $multiSourceInventorySupported = false;

    private $getStockSourceLinks;

    private $moduleManager;

    private $productTypeBundle;

    private $productTypeConfigurableFactory;

    private $productTypeGrouped;

    private $searchCriteriaBuilder;

    private $sourceDataBySku;

    private $sourceRepository;

    private $stockItemFactory;

    private $stockResolver;

    private $stockRegistry;

    private $stockState;

    private $storeManager;


    public function __construct(
        Manager $moduleManager,
        ProductTypeBundle $productTypeBundle,
        ProductTypeConfigurableFactory $productTypeConfigurableFactory,
        ProductTypeGrouped $productTypeGrouped,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StockItemFactory $stockItemFactory,
        StockRegistryInterface $stockRegistry,
        StockStateInterface $stockState,
        StoreManagerInterface $storeManager
    ) {
        $this->moduleManager         = $moduleManager;
        $this->productTypeBundle     = $productTypeBundle;
        $this->productTypeGrouped    = $productTypeGrouped;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->stockItemFactory      = $stockItemFactory;
        $this->stockRegistry         = $stockRegistry;
        $this->stockState            = $stockState;
        $this->storeManager          = $storeManager;

        $this->productTypeConfigurableFactory = $productTypeConfigurableFactory;

        if (!self::$multiSourceInventorySupported) {
            self::$multiSourceInventorySupported =
                $this->moduleManager->isOutputEnabled('Magento_InventorySales') &&
                $this->moduleManager->isOutputEnabled('Magento_Inventory');
        }

        if (self::$multiSourceInventorySupported) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $this->stockResolver = $objectManager->create(\Magento\InventorySales\Model\StockResolver::class);

            $this->sourceRepository = $objectManager->create(
                \Magento\InventoryApi\Api\SourceRepositoryInterface::class);

            $this->getStockSourceLinks = $objectManager->create(
                \Magento\InventoryApi\Api\GetStockSourceLinksInterface::class);

            $this->sourceDataBySku = $objectManager->create(
                \Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku::class);
        }
    }

    /**
     * @param Product $product
     * @param int     $qty
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function isAvailableProductQty($product, $qty)
    {
        $stockItem         = $this->stockItemFactory->create();
        $stockItemResource = $this->stockItemFactory->create()->getResource();

        $stockItemResource->loadByProductId(
            $stockItem,
            $product->getId(),
            $this->storeManager->getStore($product->getStoreId())->getWebsite()->getId()
        );

        if (!$stockItem->getManageStock() || $stockItem->getBackorders() != StockItemInterface::BACKORDERS_NO) {
            return true;
        }

        if ($stockItem->getTypeId() == 'configurable') {
            if ($stockItem->getIsInStock()) {
                $childrenIds = [];

                $requiredChildrenIds = $this->productTypeConfigurableFactory->create()
                    ->getChildrenIds($product->getId(), true);

                foreach ($requiredChildrenIds as $groupedChildrenIds) {
                    $childrenIds = array_merge($childrenIds, $groupedChildrenIds);
                }

                $isValid = false;
                foreach ($childrenIds as $childId) {
                    $childStockItem = $this->stockItemFactory->create();

                    $childStockItemResource = $this->stockItemFactory->create()->getResource();
                    $childStockItemResource->loadByProductId(
                        $childStockItem,
                        $childId,
                        $this->storeManager->getStore($product->getStoreId())->getWebsite()->getId()
                    );

                    $childQty = $childStockItem->getQty();
                    if ($childQty >= $qty) {
                        $isValid = true;
                        break;
                    }
                }

                return $isValid;
            } else {
                return false;
            }
        } elseif ($stockItem->getTypeId() == 'grouped') {
            if ($stockItem->getIsInStock()) {
                $childrenIds = [];

                $requiredChildrenIds = $this->productTypeGrouped->getChildrenIds($product->getId(), true);

                foreach ($requiredChildrenIds as $groupedChildrenIds) {
                    $childrenIds = array_merge($childrenIds, $groupedChildrenIds);
                }

                $isValid = false;
                foreach ($childrenIds as $childId) {
                    $childStockItem = $this->stockItemFactory->create();

                    $childStockItemResource = $this->stockItemFactory->create()->getResource();
                    $childStockItemResource->loadByProductId(
                        $childStockItem,
                        $childId,
                        $this->storeManager->getStore($product->getStoreId())->getWebsite()->getId()
                    );

                    $childQty = $childStockItem->getQty();
                    if ($childQty >= $qty) {
                        $isValid = true;
                        break;
                    }
                }

                return $isValid;
            }

            return $this->isBundleQty($product, $qty);
        } elseif ($stockItem->getTypeId() == 'bundle') {
            return $this->isBundleQty($product, $qty);
        } elseif ($stockItem->getTypeId() == 'virtual') {
            return $product->getTypeInstance()->isSalable($product);
        } else {
            if (self::$multiSourceInventorySupported) {
                $websiteId   = $this->storeManager->getStore($product->getStoreId())->getWebsiteId();
                $websiteCode = $this->storeManager->getWebsite($websiteId)->getCode();

                $stockId = $this->stockResolver->execute(
                    \Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE,
                    $websiteCode
                )->getStockId();

                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter(\Magento\InventoryApi\Api\Data\StockSourceLinkInterface::STOCK_ID, $stockId)
                    ->create();

                $searchResult = $this->getStockSourceLinks->execute($searchCriteria);

                $stockData = $this->sourceDataBySku->execute($product->getSku());
                foreach ($searchResult->getItems() as $result) {
                    $source = $this->sourceRepository->get($result->getSourceCode());

                    if ($source->isEnabled()) {

                        foreach ($stockData as $stockItem) {

                            if ($stockItem['source_code'] == $source->getSourceCode()) {
                                return $stockItem['quantity'] >= $qty;
                            }
                        }
                    }
                }
            } else {
                return $stockItem->getQty() >= $qty;
            }
        }
    }

    /**
     * @param Product $product
     * @param int     $qty
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function isBundleQty($product, $qty)
    {
        $optionCollection = $this->productTypeBundle->getOptionsCollection($product);

        if (!count($optionCollection->getItems())) {
            return false;
        }

        $requiredOptionIds = [];

        foreach ($optionCollection->getItems() as $option) {
            if ($option->getRequired()) {
                $requiredOptionIds[$option->getId()] = 0;
            }
        }

        $selectionCollection = $this->productTypeBundle->getSelectionsCollection($optionCollection->getAllIds(), $product);

        if (!count($selectionCollection->getItems())) {
            return false;
        }
        $salableSelectionCount = 0;
        foreach ($selectionCollection as $selection) {
            /* @var $selection \Magento\Catalog\Model\Product */
            if ($selection->isSalable()) {
                if ($this->stockRegistry->getStockItem($selection->getId())->getManageStock()) {
                    if ($selection->getSelectionQty() * $qty <= $this->stockState->getStockQty($selection->getId())) {
                        $requiredOptionIds[$selection->getOptionId()] = 1;
                        $salableSelectionCount++;
                    }
                } else {
                    if (!$selection->hasSelectionQty() || $selection->getSelectionCanChangeQty() ||
                        $selection->isInStock()
                    ) {
                        $requiredOptionIds[$selection->getOptionId()] = 1;
                        $salableSelectionCount++;
                    }

                }
            }
        }

        return array_sum($requiredOptionIds) == count($requiredOptionIds) && $salableSelectionCount;
    }
}
