<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Bestsellers
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
declare(strict_types=1);

namespace Plumrocket\Bestsellers\Model\ResourceModel;

class ProductIdsProvider implements \Plumrocket\Bestsellers\Api\ProductIdsProviderInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Plumrocket\Bestsellers\Model\ResourceModel\BestsellersCollectionFactory
     */
    private $bestsellersCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Plumrocket\Bestsellers\Model\Report\Interval
     */
    private $interval;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    private $productVisibility;

    /**
     * @var \Plumrocket\Bestsellers\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\Bestsellers\Api\PeriodListInterface
     */
    private $periodList;

    /**
     * @var array
     */
    private $fallbackStrategies;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    private $productStatus;

    /**
     * ProductIdsProvider constructor.
     *
     * @param \Psr\Log\LoggerInterface                                                 $logger
     * @param \Plumrocket\Bestsellers\Model\ResourceModel\BestsellersCollectionFactory $bestsellersCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface                               $storeManager
     * @param \Plumrocket\Bestsellers\Model\Report\Interval                            $interval
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface                         $categoryRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory           $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility                                $productVisibility
     * @param \Plumrocket\Bestsellers\Helper\Data                                      $dataHelper
     * @param \Plumrocket\Bestsellers\Api\PeriodListInterface                          $periodList
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status                   $productStatus
     * @param array                                                                    $fallbackStrategies
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Plumrocket\Bestsellers\Model\ResourceModel\BestsellersCollectionFactory $bestsellersCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Plumrocket\Bestsellers\Model\Report\Interval $interval,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Plumrocket\Bestsellers\Helper\Data $dataHelper,
        \Plumrocket\Bestsellers\Api\PeriodListInterface $periodList,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        array $fallbackStrategies = []
    ) {
        $this->logger = $logger;
        $this->fallbackStrategies = $fallbackStrategies;
        $this->storeManager = $storeManager;
        $this->interval = $interval;
        $this->categoryRepository = $categoryRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->dataHelper = $dataHelper;
        $this->periodList = $periodList;
        $this->bestsellersCollectionFactory = $bestsellersCollectionFactory;
        $this->productStatus = $productStatus;
    }

    /**
     * @inheritDoc
     */
    public function getByPeriod(string $period, int $count = 5, string $fallback = self::FALLBACK_EMPTY) : array
    {
        if (! $this->dataHelper->moduleEnabled($this->getStoreId())) {
            return [];
        }

        $productIds = $this->get($period, $count);

        return $this->applyFallback($productIds, $count, $fallback, $this->getStoreId());
    }

    /**
     * @inheritDoc
     */
    public function getDaily(int $count = 5, string $fallback = self::FALLBACK_EMPTY) : array
    {
        return $this->getByPeriod(self::PERIOD_DAY, $count, $fallback);
    }

    /**
     * @inheritDoc
     */
    public function getWeekly(int $count = 5, string $fallback = self::FALLBACK_EMPTY) : array
    {
        return $this->getByPeriod(self::PERIOD_WEEK, $count, $fallback);
    }

    /**
     * @inheritDoc
     */
    public function getMonthly(int $count = 5, string $fallback = self::FALLBACK_EMPTY) : array
    {
        return $this->getByPeriod(self::PERIOD_MONTH, $count, $fallback);
    }

    /**
     * @inheritDoc
     */
    public function getYearly(int $count = 5, string $fallback = self::FALLBACK_EMPTY) : array
    {
        return $this->getByPeriod(self::PERIOD_YEAR, $count, $fallback);
    }

    /**
     * @inheritDoc
     */
    public function getByCategory(
        string $period,
        int $categoryId,
        int $count = 5,
        string $fallback = self::FALLBACK_EMPTY
    ) : array {
        $storeId = $this->getStoreId();

        if (! $this->dataHelper->moduleEnabled($storeId)) {
            return [];
        }

        try {
            $category = $this->categoryRepository->get($categoryId);

            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = $this->productCollectionFactory->create();

            $productCollection->setStoreId(
                $storeId
            )->addCategoryFilter(
                $category
            )->addAttributeToFilter(
                'status',
                ['in' => $this->productStatus->getVisibleStatusIds()]
            )->setVisibility(
                $this->productVisibility->getVisibleInCatalogIds()
            )->addFieldToSelect(
                'entity_id'
            );

            $idPool = $productCollection->getColumnValues('entity_id');
        } catch (\Magento\Framework\Exception\NoSuchEntityException $noSuchEntityException) {
            $idPool = [];
            $categoryId = 0;
        }

        $productIds = $this->get($period, $count, [], $idPool);

        return $this->applyFallback($productIds, $count, $fallback, $storeId, $categoryId);
    }

    /**
     * @param string $period
     * @param int    $count
     * @param array  $exclude
     * @param array  $idPool
     * @return array
     */
    public function get(
        string $period,
        int $count = 5,
        array $exclude = [],
        array $idPool = []
    ) : array {
        if (0 === $count) {
            return [];
        }

        $storeId = $this->getStoreId();

        try {
            $periodTable = $this->getPeriodTable($period);
        } catch (\Magento\Framework\Exception\LocalizedException $localizedException) {
            $this->logger->critical($localizedException);
            return [];
        }

        /** @var \Plumrocket\Bestsellers\Model\ResourceModel\BestsellersCollection $collection */
        $collection = $this->bestsellersCollectionFactory->create();
        $collection->setPeriod($periodTable);
        $collection->addStoreFilter($storeId);

        if ($interval = $this->interval->getByPeriod($period)) {
            $collection->setDateRange(
                $interval['start']->format('Y-m-d'),
                $interval['end']->format('Y-m-d')
            );
        }

        if (! empty($exclude)) {
            $collection->addFieldToFilter('product_id', ['nin' => $exclude]);
        }

        if (! empty($idPool)) {
            $collection->addFieldToFilter('product_id', ['in' => [33]]);
        }

        $reportSelect = $collection
            ->loadSelect()
            ->getSelect();

        $connection = $collection->getConnection();

        $wrapperSelect = $connection->select()
            ->from(
                ['report' => $reportSelect],
                false
            )
            ->columns(
                ['id' => new \Zend_Db_Expr('IF(linked.parent_id IS NULL, report.product_id, linked.parent_id)')] //@codingStandardsIgnoreLine
            )
            ->joinLeft(
                ['linked' => $collection->getResource()->getTable('catalog_product_super_link')],
                'linked.product_id = report.product_id',
                false
            )
            ->limit(
                $count
            );

        return array_unique($connection->fetchCol($wrapperSelect));
    }

    /**
     * @param string $period
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPeriodTable(string $period) : string
    {
        $mapping = $this->periodList->getPeriods();

        if (isset($mapping[$period]['table'])) {
            return $mapping[$period]['table'];
        }

        throw new \Magento\Framework\Exception\LocalizedException(__('Invalid bestsellers period "%1"', $period));
    }

    /**
     * @param array $productIds
     * @param int   $count
     * @param       $fallback
     * @param int   $storeId
     * @param int   $categoryId
     * @return array
     */
    private function applyFallback(array $productIds, int $count, $fallback, int $storeId, int $categoryId = 0) : array
    {
        if (empty($productIds) && isset($this->fallbackStrategies[$fallback])) {
            $strategy = $this->fallbackStrategies[$fallback];
            if ($strategy instanceof \Plumrocket\Bestsellers\Model\FallbackStrategyInterface) {
                $productIds = $strategy->generateIdList($productIds, $count, $storeId, $categoryId);
            } else {
                try {
                    $productIds = $strategy->generateIdList($productIds, $count, $storeId, $categoryId);
                } catch (\Exception $exception) {
                    $this->logger->critical($exception);
                }
            }
        }

        return $productIds;
    }

    /**
     * @return int
     */
    private function getStoreId() : int
    {
        try {
            $storeId = (int) $this->storeManager->getStore()->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $noSuchEntityException) {
            $storeId = 0;
        }
        return $storeId;
    }
}
