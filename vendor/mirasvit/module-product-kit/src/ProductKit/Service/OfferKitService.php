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



namespace Mirasvit\ProductKit\Service;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory as ProductTypeConfigurableFactory;
use Magento\Framework\Registry;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Data\OfferKit;
use Mirasvit\ProductKit\Data\OfferKitFactory;
use Mirasvit\ProductKit\Data\OfferKitItem;
use Mirasvit\ProductKit\Data\OfferKitItemFactory;
use Mirasvit\ProductKit\Model\ConfigProvider;
use Mirasvit\ProductKit\Repository\IndexRepository;
use Mirasvit\ProductKit\Repository\KitRepository;
use Mirasvit\ProductKit\Service\Product\Bundle\PriceService;
use Mirasvit\ProductKit\Service\Product\CheckQtyService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class OfferKitService
{
    private $checkQtyService;

    private $configProvider;

    private $kitBuilderService;

    private $kitRepository;

    private $indexRepository;

    private $offerKitFactory;

    private $offerKitItemFactory;

    private $priceService;

    private $productCollectionFactory;

    private $productRepository;

    private $productTypeConfigurableFactory;

    private $registry;

    private $checkoutSession;

    public function __construct(
        CheckQtyService $checkQtyService,
        ConfigProvider $configProvider,
        KitBuilderService $kitBuilderService,
        KitRepository $kitRepository,
        IndexRepository $indexRepository,
        OfferKitFactory $offerKitFactory,
        OfferKitItemFactory $offerKitItemFactory,
        PriceService $priceService,
        ProductCollectionFactory $productCollectionFactory,
        ProductRepository $productRepository,
        ProductTypeConfigurableFactory $productTypeConfigurableFactory,
        Registry $registry,
        CheckoutSession $checkoutSession
    ) {
        $this->checkQtyService          = $checkQtyService;
        $this->configProvider           = $configProvider;
        $this->kitBuilderService        = $kitBuilderService;
        $this->kitRepository            = $kitRepository;
        $this->indexRepository          = $indexRepository;
        $this->offerKitFactory          = $offerKitFactory;
        $this->offerKitItemFactory      = $offerKitItemFactory;
        $this->priceService             = $priceService;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productRepository        = $productRepository;
        $this->registry                 = $registry;
        $this->checkoutSession          = $checkoutSession;

        $this->productTypeConfigurableFactory = $productTypeConfigurableFactory;
    }

    /**
     * @return Product|false
     */
    public function getContextProduct()
    {
        $product = $this->registry->registry('current_product');

        if (!$product) {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            $item = $this->checkoutSession->getQuote()->getItemsCollection()->getLastItem();

            if (!$item->getId()) {
                return false;
            }

            if ($item->getBuyRequest()->getData('kit_id')) {
                # the item already part of added kit
                return false;
            }

            if ($item->getParentItem()) {
                $product = $item->getParentItem()->getProduct();
            } else {
                $product = $item->getProduct();
            }
        }

        return $product;
    }

    /**
     * @param int $productId
     * @param int $customerGroupId
     * @param int $storeId
     *
     * @return OfferKit[]
     */
    public function findSuitableKits($productId, $customerGroupId, $storeId)
    {
        $connection = $this->indexRepository->select()->getConnection();

        $indexQuery = $this->indexRepository->select();
        $indexQuery
            ->columns('kit.kit_id')
            ->where('index.product_id = ?', (int)$productId)
            ->where('index.is_primary = ?', 1)
            ->where('find_in_set(?, kit.store_ids) OR kit.store_ids = 0', (int)$storeId)
            ->where('find_in_set(?, kit.customer_group_ids)', (int)$customerGroupId)
            ->group('kit.kit_id');

        $kitIds = $connection->fetchCol($indexQuery);
        if (count($kitIds) === 0) {
            return [];
        }

        $collection = $this->kitRepository->getCollection();

        $collection->addFieldToFilter(KitInterface::ID, [$kitIds])
            ->addFilterByCustomerGroupId($customerGroupId)
            ->addFilterByStoreId($storeId)
            ->addFieldToFilter(KitInterface::IS_ACTIVE, 1);
        $collection->getSelect()->order(KitInterface::PRIORITY);

        $result = [];

        $kitsLimit = $this->configProvider->getOfferKitsLimit();

        foreach ($collection as $kit) {
            if (count($result) >= $kitsLimit) {
                break;
            }

            $offerKit = $this->createOfferKit($kit, $productId, $storeId);

            if (count($offerKit->getItems())) {
                $offerKit->setBlockId($kit->getId());

                $result[$offerKit->getHash()] = $offerKit;
            }

            if (count($result) >= $kitsLimit) {
                break;
            }
        }

        return array_values($result);
    }

    /**
     * @param KitInterface $kit
     * @param int          $primaryProductId
     * @param int          $storeId
     *
     * @return OfferKit
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function createOfferKit(KitInterface $kit, $primaryProductId, $storeId = 0)
    {
        $offerKit = $this->offerKitFactory->create();
        $offerKit->setKit($kit);

        $items = $this->kitRepository->getItemRepository()->getItems($kit);

        $combinations = $this->kitBuilderService->getItemCombinations($items);

        // find suitable combination
        $offerKit->setItems($this->findCombinationForProduct($items, $combinations, $primaryProductId, $storeId));

        $hash              = '';
        $offerCombinations = [];

        foreach ($combinations as $combination) {
            $combinationHash  = [];
            $combinationItems = [];

            $offerItems = $this->kitBuilderService->getOfferItems($items, $combination);

            $productId = 0;
            foreach ($offerItems as $offerItem) {

                $productId = 0;
                $itemsSet  = [];

                foreach ($offerKit->getItems() as $itm) {
                    if ($itm->getId() === $offerItem->getId()) {
                        $productId = $itm->getProductId();
                        $itemsSet  = $itm->getItemVariations();
                    }
                }

                if ($productId === 0) {
                    continue;
                }

                $combinationHash[] = $offerItem->getId() . '-' . $productId;

                $offerItem->setProductId($productId);
                $offerItem->setFinalPrice($this->getProductPrice($offerItem->getProduct(), $offerItem->getQty()));

                $offerItem->setItemVariations($itemsSet);

                $combinationItems[] = $offerItem;
            }

            // combination does not have enough products
            if ($productId === 0) {
                continue;
            }

            if ($primaryProductId == $combinationItems[0]->getProductId() && count($items) == count($combinationHash)) {
                $hash = implode('_', $combinationHash);

                $offerKit->setMainCombinationHash(implode('/', $combinationHash));
            }

            $combinationHash = implode('/', $combinationHash);

            $offerCombinations[$combinationHash] = $combinationItems;

            $this->buildCombinationsForVariations($offerCombinations, $combinationItems, 0);
        }

        $offerKit->setCombinations($offerCombinations);

        $offerKit->setHash($hash);

        return $offerKit;
    }

    /**
     * @param array $offerCombinations
     * @param array $combinationItems
     * @param int   $k
     */
    public function buildCombinationsForVariations(&$offerCombinations, $combinationItems, $k)
    {
        while ($k < count($combinationItems)) {
            foreach ($combinationItems[$k]->getItemVariations() as $variationItem) {
                $newCombination = [];
                foreach ($combinationItems as $combinationItem) {
                    $newCombination[] = clone $combinationItem;
                }

                $newCombination[$k]->setData(OfferKitItem::PRODUCT_ID, $variationItem->getData(OfferKitItem::PRODUCT_ID));
                $newCombination[$k]->setData(OfferKitItem::FINAL_PRICE, $variationItem->getData(OfferKitItem::FINAL_PRICE));

                $combinationHash = [];
                foreach ($newCombination as $newItems) {
                    $combinationHash[] = $newItems->getId() . '-' . $newItems->getProductId();
                }

                $combinationHash = implode('/', $combinationHash);

                if (!isset($offerCombinations[$combinationHash])) {
                    $offerCombinations[$combinationHash] = $newCombination;
                }

                $this->buildCombinationsForVariations($offerCombinations, $newCombination, $k + 1);
            }

            $k++;
        }
    }

    /**
     * @param KitItemInterface[] $items
     * @param array              $combinations
     * @param int                $primaryProductId
     * @param int                $storeId
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function findCombinationForProduct($items, $combinations, $primaryProductId, $storeId = 0)
    {
        $offerItems = [];
        foreach ($combinations as $combination) {
            $offerItems   = [];
            $usedProducts = [];

            foreach ($combination as $item) {
                $offerItem = $this->createOfferItem($item, $usedProducts, $primaryProductId, $storeId);

                if (!$offerItem) {
                    continue;
                }

                if (!$offerItem->getProduct()->isVisibleInSiteVisibility()) {
                    $offerItems = []; // do not show kits with invisible items
                    break;
                }

                $usedProducts[] = $offerItem->getProductId();

                if ($offerItem->getProductId() != $primaryProductId) {
                    $offerItem->setItemVariations($this->getItemVariations($item, $usedProducts));
                }

                $offerItems[] = $offerItem;
            }

            if (count($offerItems) == count($items)) {
                break;
            }

            $offerItems = [];
        }

        return $offerItems;
    }


    /**
     * @param KitItemInterface $item
     * @param array            $usedProducts
     *
     * @return OfferKitItem[]
     */
    private function getItemVariations($item, $usedProducts)
    {
        $productsPerPosition = 1;
        if ($this->configProvider->getProductsPerPosition() > 0) {
            $productsPerPosition = $this->configProvider->getProductsPerPosition();
        }

        $itemsSet = [];
        for ($i = 0; $i < $productsPerPosition; $i++) {
            $offerItem = $this->createOfferItem($item, $usedProducts, 0);
            if (!$offerItem) {
                break;
            }

            $usedProducts[] = $offerItem->getProductId();

            $itemsSet[] = $offerItem;
        }

        return $itemsSet;
    }

    /**
     * @param KitItemInterface|OfferKitItem $item
     * @param array                         $usedProducts
     * @param int                           $preferredProductId
     * @param int                           $storeId
     *
     * @return OfferKitItem|null
     */
    private function createOfferItem($item, $usedProducts, $preferredProductId, $storeId = 0)
    {
        $productId = $this->getUniqueProductIdByItem($item, $usedProducts, $preferredProductId, $storeId);
        if (!$productId) {
            return null;
        }

        $offerItem = $this->offerKitItemFactory->create();

        $offerItem->setItem($item)
            ->setProductId($productId);

        $offerItem->setFinalPrice($this->getProductPrice($offerItem->getProduct(), $item->getQty()));

        return $offerItem;
    }

    /**
     * @param Product $product
     * @param int     $qty
     *
     * @return float
     */
    public function getProductPrice($product, $qty = 1)
    {
        if ($product->getTypeId() == 'bundle') {
            $price = $this->priceService->getDisplayPrice($product, 1);
        } elseif ($product->getTypeId() == 'configurable') {
            $price = $product->getFinalPrice(1);
        } elseif ($product->getTypeId() == 'grouped') {
            $minProduct = $product
                ->getPriceInfo()
                ->getPrice(FinalPrice::PRICE_CODE)
                ->getMinProduct();

            $price = 0;
            if ($minProduct) {
                $price = $minProduct->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getAmount()->getValue();
            }
        } else {
            $price = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        }

        $price = round($price * $qty, 2);

        return $price;
    }

    /**
     * @param KitItemInterface|OfferKitItem $item
     * @param array                         $usedProducts
     * @param int                           $preferredProductId
     * @param int                           $storeId
     *
     * @return int
     */
    private function getUniqueProductIdByItem($item, $usedProducts, $preferredProductId, $storeId = 0)
    {
        $where = ['1'];

        if ($usedProducts) {
            $where[] = 'index.product_id NOT IN(' . implode(',', $usedProducts) . ')';
        }

        if ($preferredProductId) {
            $where[] = 'index.product_id = ' . (int)$preferredProductId;
        }

        $connection = $this->indexRepository->selectWithProductVisibility()->getConnection();
        $indexQuery = $this->indexRepository->selectWithProductVisibility($storeId);
        $indexQuery
            ->columns('index.product_id')
            ->where('index.item_id = ? AND cpvd.value > 1', (int)$item->getId())
            ->where(implode(' AND ', $where))
            ->limit(1)
            ->orderRand();

        $productId = $connection->fetchOne($indexQuery);

        if (!$productId && $preferredProductId) {
            $productId = $this->getUniqueProductIdByItem($item, $usedProducts, 0, $storeId);
        }

        if ($productId > 0) {
            $collection = $this->productCollectionFactory->create();
            $collection->addFieldToFilter('entity_id', $productId);

            $item->getRule()->getMatchedProductIds($collection);

            $product = $this->productRepository->getById($productId);
            if (!$collection->count() || !$this->checkQtyService->isAvailableProductQty($product, $item->getQty())
            ) {
                if ($preferredProductId != $productId) {
                    $usedProducts[] = $productId;
                    $productId      = $this->getUniqueProductIdByItem($item, $usedProducts, 0);
                } else {
                    // primary product is out of stock
                    $productId = 0;
                }
            }
        }

        return $productId;
    }
}
