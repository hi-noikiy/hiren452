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

namespace Plumrocket\Bestsellers\Model\Report\Fallback;

/**
 * Class RandomStrategy
 *
 * Generate random list instead of bestsellers
 */
class RandomStrategy implements \Plumrocket\Bestsellers\Model\FallbackStrategyInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    private $productVisibility;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * RandomStrategy constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility                      $productVisibility
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface               $categoryRepository
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->productVisibility = $productVisibility;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @inheritDoc
     */
    public function generateIdList(array $productIds, int $limit, int $storeId, int $categoryId = 0) : array
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $this->collectionFactory->create();
        $productCollection->setVisibility($this->productVisibility->getVisibleInCatalogIds())
                          ->addFieldToSelect('entity_id')
                          ->addStoreFilter($storeId)->setPageSize($limit);

        $productCollection->getSelect()->orderRand();

        if ($categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                $productCollection->addCategoryFilter($category);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $noSuchEntityException) {}
        }

        return $productCollection->getColumnValues('entity_id');
    }
}
