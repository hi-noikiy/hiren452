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

use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;

class SuggestionKitService
{
    const FILTER_KIT_MAX_PRODUCTS = 'max_kit_products';
    const FILTER_KIT_MIN_PRODUCTS = 'min_kit_products';
    const FILTER_MAX_ORDERS       = 'max_orders';
    const FILTER_MIN_ORDERS       = 'min_orders';
    const FILTER_MAX_KIT_PRICE    = 'max_kit_price';
    const FILTER_MIN_KIT_PRICE    = 'min_kit_price';
    const FILTER_SAME_ORDER       = 'same_order';
    const FILTER_SAME_CUSTOMER    = 'same_customer';

    const KIT_COMBINATION_KEY_LABEL     = 'label';
    const KIT_COMBINATION_KEY_SKU       = 'sku';
    const KIT_COMBINATION_KEY_IMAGE     = 'image';
    const KIT_COMBINATION_KEY_PRICE     = 'price';
    const KIT_COMBINATION_KEY_KIT_PRICE = 'kit_price';

    const PRODUCTS_KEY_KIT       = 'kit';
    const PRODUCTS_KEY_STATISTIC = 'statistic';

    const STATISTIC_KEY_ORDER_AMOUNT         = 'order_amount';
    const STATISTIC_KEY_UNIQ_CUSTOMER_AMOUNT = 'uniq_customer_amount';
    const STATISTIC_KEY_AVR_ORDER_ITEMS      = 'avr_order_items';
    const STATISTIC_KEY_AVR_ORDER_TOTAL      = 'avr_order_total';

    const SUGGESTION_LIMIT = 50;

    const SUGGESTION_PROCESSING_TIME_MS = 3;

    private $products = [];

    private $productsInfo = [];

    private $imageHelper;

    private $orderItemCollectionFactory;

    private $offerKitService;

    private $priceCurrency;

    private $priceHelper;

    private $productRepository;

    private $resource;

    private $timer;

    public function __construct(
        ImageHelper $imageHelper,
        PriceCurrencyInterface $priceCurrency,
        PriceHelper $priceHelper,
        ProductRepository $productRepository,
        Suggester\Timer $timer,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        OfferKitService $offerKitService,
        ResourceConnection $resource
    ) {
        $this->imageHelper = $imageHelper;

        $this->priceCurrency   = $priceCurrency;
        $this->priceHelper     = $priceHelper;
        $this->offerKitService = $offerKitService;
        $this->timer           = $timer;

        $this->productRepository          = $productRepository;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;

        $this->resource = $resource;
    }

    /**
     * @param array $filters
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getProductsByFilters($filters)
    {
        $orderItemCollection = $this->orderItemCollectionFactory->create();

        $orderItemQuery = $orderItemCollection->getSelect();

        $orderItemQuery->reset('columns');
        $orderItemQuery->columns([
            'order_id'    => 'main_table.order_id',
            'customer_id' => 'sales_order.customer_id',
            'product_ids' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT main_table.product_id)'),

        ]);
        $orderItemQuery->joinInner(
            ['sales_order' => $this->resource->getTableName('sales_order')],
            'main_table.order_id = sales_order.entity_id',
            []
        );

        $orderItemQuery->where('main_table.parent_item_id = 0 OR main_table.parent_item_id IS NULL');

        $itemsMinAmount = $filters[self::FILTER_KIT_MIN_PRODUCTS] > 1 ? $filters[self::FILTER_KIT_MIN_PRODUCTS] : 2;
        $itemsMaxAmount = $filters[self::FILTER_KIT_MAX_PRODUCTS] > 1 ? $filters[self::FILTER_KIT_MAX_PRODUCTS] : 99;

        $orderItemQuery->having('COUNT(main_table.item_id) >= ?', $itemsMinAmount);

        if (!empty($filters[self::FILTER_MAX_KIT_PRICE])) {
            // decrease selected rows
            $orderItemQuery->where('main_table.base_price <= ?', $filters[self::FILTER_MAX_KIT_PRICE]);
        }

        $orderItemQuery->group('main_table.order_id');

        $select = $this->resource->getConnection()->select();
        $select
            ->reset('columns')
            ->from(['suggestions' => $orderItemQuery])
            ->columns([
                'order_count'  => new \Zend_Db_Expr('COUNT(order_id)'),
                'order_ids'    => new \Zend_Db_Expr('COUNT(order_id)'),
                'customer_ids' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT customer_id)'),
            ])
            ->order('order_count')
            ->limit(self::SUGGESTION_LIMIT)
        ;
        if (!empty($filters[self::FILTER_SAME_CUSTOMER])) {
            $select->columns([
                    'product_ids' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT product_ids)')
                ])
                ->group('customer_id');
        } else {
            $select->columns('product_ids')
                ->group('product_ids');
        }

        if (!empty($filters[self::FILTER_MIN_ORDERS])) {
            $select->having('order_count >= ?', (int)$filters[self::FILTER_MIN_ORDERS]);
        }

        if (!empty($filters[self::FILTER_MAX_ORDERS])) {
            $select->having('order_count <= ?', (int)$filters[self::FILTER_MAX_ORDERS]);
        }

        /** @var \Zend_Db_Statement $query */
        $query = $select->query();
        $data = $query->fetchAll();

        $this->timer->start(self::SUGGESTION_PROCESSING_TIME_MS);

        $combinations = [];
        foreach ($data as $row) {
            $productIds = array_values(array_unique(explode(',', $row['product_ids'])));

            for ($limit = $itemsMinAmount; $limit <= $itemsMaxAmount; $limit++) {

                for ($i = 0; $i < count($productIds); $i++) {
                    $combination = [$productIds[$i]];

                    for ($j = $i + 1; $j < count($productIds); $j++) {
                        $combination[] = $productIds[$j];

                        if (count($combination) >= $limit) {
                            sort($combination);
                            // prevent combination doubling
                            $combinations[implode('_', $combination)] = $combination;
                            array_pop($combination);

                            if ($this->timer->isTimeout()) {
                                break 4;
                            }
                        }
                    }
                }
            }
        }

        $this->timer->start(self::SUGGESTION_PROCESSING_TIME_MS);

        $products = [];
        foreach ($combinations as $combination) {
            $kitPrice = 0;

            $kit = [];

            try {
                foreach ($combination as $productId) {
                    $info = $this->getProductsInfo($productId);

                    $kit[] = $info;

                    $kitPrice += $info[self::KIT_COMBINATION_KEY_KIT_PRICE];
                }
            } catch (NoSuchEntityException $e) {
                continue;
            }

            $isValidKitPrice = true;
            if (!empty($filters[self::FILTER_MIN_KIT_PRICE])) {
                if ($kitPrice < (float)$filters[self::FILTER_MIN_KIT_PRICE]) {
                    $isValidKitPrice = false;
                }
            }
            if (!empty($filters[self::FILTER_MAX_KIT_PRICE])) {
                if ($kitPrice > (float)$filters[self::FILTER_MAX_KIT_PRICE]) {
                    $isValidKitPrice = false;
                }
            }

            if (!$this->timer->isTimeout()) {
                if (!empty($filters[self::FILTER_SAME_CUSTOMER])) {
                    $statistic = $this->getCustomerCombinationStatistic($combination);
                } else {
                    $statistic = $this->getOrderCombinationStatistic($combination);
                }
            } else {
                $statistic = [
                    self::STATISTIC_KEY_ORDER_AMOUNT         => '',
                    self::STATISTIC_KEY_UNIQ_CUSTOMER_AMOUNT => '',
                    self::STATISTIC_KEY_AVR_ORDER_ITEMS      => '',
                    self::STATISTIC_KEY_AVR_ORDER_TOTAL      => '',
                ];
            }

            if ($isValidKitPrice) {
                $products[] = [
                    self::PRODUCTS_KEY_KIT       => $kit,
                    self::PRODUCTS_KEY_STATISTIC => $statistic,
                ];
            }
        }

        usort($products, function($a, $b) {
            if ($a[self::PRODUCTS_KEY_STATISTIC]['order_amount'] == $b[self::PRODUCTS_KEY_STATISTIC]['order_amount']) {
                return 0;
            }

            return ($a[self::PRODUCTS_KEY_STATISTIC]['order_amount'] > $b[self::PRODUCTS_KEY_STATISTIC]['order_amount']) ? -1 : 1;
        });

        $products = array_slice($products, 0, self::SUGGESTION_LIMIT);

        return $products;
    }

    /**
     * @param array $productIds
     *
     * @return array
     */
    private function getCustomerCombinationStatistic($productIds)
    {
        sort($productIds);

        $orderItemCollection = $this->orderItemCollectionFactory->create();

        $orderItemCollection->getSelect()
            ->reset('columns')
            ->columns([
                'order_ids' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT order_id)'),
            ])
            ->joinInner(['sales_order' => $orderItemCollection->getTable('sales_order')], 'main_table.order_id = sales_order.entity_id', '')
            ->group('customer_id')
            ->having(new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT product_id ORDER BY product_id ASC) = "' . implode(',', $productIds) . '"'));

        foreach ($productIds as $productId) {
            $orderItemCollection->getSelect()
                ->orWhere('product_id = ' . $productId);
        }

        $data = $orderItemCollection->getColumnValues('order_ids');


        $orderIds = [];
        foreach ($data as $row) {
            $orderIds = array_merge($orderIds, explode(',', $row));
        }

        $orderItemCollection = $this->orderItemCollectionFactory->create();

        $orderItemCollection->getSelect()
            ->reset('columns')
            ->columns('sales_order.customer_id')
            ->columns('sales_order.grand_total')
            ->columns([
                'qty_ordered_amount' => new \Zend_Db_Expr('COUNT(`main_table`.`qty_ordered`)'),
            ])
            ->joinInner(['sales_order' => $orderItemCollection->getTable('sales_order')], 'main_table.order_id = sales_order.entity_id', '')
            ->where('main_table.order_id IN (' . implode(',', $orderIds) . ')')
            ->where('main_table.parent_item_id = 0 OR main_table.parent_item_id IS NULL')
            ->group('order_id')
        ;

        $data = $orderItemCollection->getData();

        $orderAmount   = count($data);
        $avrOrderTotal = array_sum(array_column($data, 'grand_total')) / $orderAmount;
        $avrOrderItems = array_sum(array_column($data, 'qty_ordered_amount')) / $orderAmount;

        $result = [
            self::STATISTIC_KEY_ORDER_AMOUNT         => $orderAmount,
            self::STATISTIC_KEY_UNIQ_CUSTOMER_AMOUNT => count(array_unique(array_column($data, 'customer_id'))),
            self::STATISTIC_KEY_AVR_ORDER_ITEMS      => round($avrOrderItems, 2),
            self::STATISTIC_KEY_AVR_ORDER_TOTAL      => $this->priceHelper->currency($avrOrderTotal, true, false),
        ];

        return $result;
    }

    /**
     * @param array $productIds
     *
     * @return array
     */
    private function getOrderCombinationStatistic($productIds)
    {
        $orderItemCollection = $this->orderItemCollectionFactory->create();
        $orderItemCollection->getSelect()
            ->reset('columns')
            ->columns('order_id')
            ->group('order_id')
        ;
        foreach ($productIds as $productId) {
            $orderItemCollection->getSelect()
                ->having('FIND_IN_SET(?, GROUP_CONCAT(DISTINCT product_id))', $productId)
            ;
        }
        $orderIdSubQuery = $orderItemCollection->getSelectSql(true);

        $orderItemCollection = $this->orderItemCollectionFactory->create();
        $orderItemCollection->getSelect()
            ->reset('columns')
            ->columns([
                'sales_order.customer_id',
                'sales_order.grand_total',
                'qty_ordered_amount' => new \Zend_Db_Expr('COUNT(`main_table`.`qty_ordered`)'),
            ])
            ->joinInner(['sales_order' => $orderItemCollection->getTable('sales_order')], 'main_table.order_id = sales_order.entity_id', '')
            ->where('main_table.order_id IN (' . $orderIdSubQuery . ')')
            ->where('main_table.parent_item_id = 0 OR main_table.parent_item_id IS NULL')
            ->group('order_id')
        ;

        $data = $orderItemCollection->getData();

        $orderAmount   = count($data);
        $avrOrderTotal = array_sum(array_column($data, 'grand_total')) / $orderAmount;
        $avrOrderItems = array_sum(array_column($data, 'qty_ordered_amount')) / $orderAmount;

        $result = [
            self::STATISTIC_KEY_ORDER_AMOUNT         => $orderAmount,
            self::STATISTIC_KEY_UNIQ_CUSTOMER_AMOUNT => count(array_unique(array_column($data, 'customer_id'))),
            self::STATISTIC_KEY_AVR_ORDER_ITEMS      => round($avrOrderItems, 2),
            self::STATISTIC_KEY_AVR_ORDER_TOTAL      => $this->priceHelper->currency($avrOrderTotal, true, false),
        ];

        return $result;
    }

    /**
     * @param int $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductsById($productId)
    {
        if (!isset($this->products[$productId])) {
            $this->products[$productId] = $this->productRepository->getById($productId);
        }

        return $this->products[$productId];
    }

    /**
     * @param int $productId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductsInfo($productId)
    {
        if (!isset($this->productsInfo[$productId])) {
            $product = $this->getProductsById($productId);

            $kitPrice = $this->offerKitService->getProductPrice($product);

            $this->productsInfo[$productId] = [
                self::KIT_COMBINATION_KEY_LABEL     => $product->getName(),
                self::KIT_COMBINATION_KEY_SKU       => $product->getSku(),
                self::KIT_COMBINATION_KEY_IMAGE     => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
                self::KIT_COMBINATION_KEY_PRICE     => $this->priceCurrency->convertAndFormat($product->getFinalPrice()),
                self::KIT_COMBINATION_KEY_KIT_PRICE => $kitPrice,
            ];
        }

        return $this->productsInfo[$productId];
    }
}
