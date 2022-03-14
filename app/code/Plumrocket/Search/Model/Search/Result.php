<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Search Autocomplete & Suggest
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Search\Model\Search;

use Plumrocket\Search\Helper\Search;

class Result extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Plumrocket\Search\Helper\Data
     */
    private $helper;

    /**
     * @var
     */
    private $queryText;

    /**
     * @var \Plumrocket\Search\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\Search\Model\ResourceModel\Result\Builder
     */
    private $searchBuilder;

    /**
     * @var \Magento\Search\Model\Search
     */
    private $search;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Magento\Framework\DB\Helper
     */
    private $resourceHelper;

    /**
     * @var \Magento\Search\Model\ResourceModel\Query\CollectionFactory
     */
    private $queryCollectionFactory;

    /**
     * @var \Magento\Review\Model\Review
     */
    private $reviewModel;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var null
     */
    private $productCollection = null;

    /**
     * @var Collection
     */
    private $collectionWithoutLimit;

    /**
     * @var array
     */
    private $categoryIds = [];

    /**
     * Result constructor.
     *
     * @param \Plumrocket\Search\Helper\Data                              $helper
     * @param \Plumrocket\Search\Helper\Config                            $config
     * @param \Plumrocket\Search\Model\ResourceModel\Result\Builder       $searchBuilder
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Magento\Catalog\Model\CategoryFactory                          $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Search\Model\ResourceModel\Query\CollectionFactory     $queryCollectionFactory
     * @param \Magento\Framework\DB\Helper                                    $resourceHelper
     * @param \Magento\Search\Model\Search                                    $search
     * @param \Magento\Framework\Model\Context                                $context
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Magento\Review\Model\Review                                    $reviewModel
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null    $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null              $resourceCollection
     * @param array                                                           $data
     */
    public function __construct(
        \Plumrocket\Search\Helper\Data $helper,
        \Plumrocket\Search\Helper\Config $config,
        \Plumrocket\Search\Model\ResourceModel\Result\Builder $searchBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Search\Model\ResourceModel\Query\CollectionFactory $queryCollectionFactory,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Search\Model\Search $search,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Review\Model\Review $reviewModel,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->registry = $registry;
        $this->searchBuilder = $searchBuilder;
        $this->resourceHelper = $resourceHelper;
        $this->reviewModel = $reviewModel;
        $this->queryCollectionFactory = $queryCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->search = $search;
        $this->reviewModel = $reviewModel;
        $this->config = $config;
        $this->helper = $helper;
    }

    /**
     * @return \Plumrocket\Search\Model\ResourceModel\Result\Builder
     */
    public function getProducts()
    {
        if ($this->productCollection) {
            return $this->productCollection;
        }

        $this->queryText = $this->helper->getQueryText();

        $this->productCollection = $this->searchBuilder
            ->addSearchFilter($this->queryText)
            ->addAttributeToSelect($this->getAttributeToSelectList())
            ->addFieldToFilter('visibility', ['gt' => 2]);

        $this->collectionWithoutLimit = clone $this->productCollection;
        $this->productCollection->setPageSize($this->config->getPSCount());

        if ($sortedIds = $this->registry->registry(Search::SORTED_IDS_KEY)) {
            $sortedIds = "'" . str_replace(
                    ",",
                    "','",
                    implode(",", $sortedIds)
                ) . "'";

            $this->productCollection->getSelect()->order(
                new \Zend_Db_Expr("FIELD(e.entity_id, " . $sortedIds . ")")
            );
        }

        $queryCategoryId = $this->helper->getQueryCategory();
        $categoryId = $queryCategoryId <= 0 ? $this->storeManager->getStore()->getRootCategoryId() : $queryCategoryId;

        if ($categoryId > 0) {
            if ($categoryForFilter = $this->categoryFactory->create()->load($categoryId)) {
                $this->productCollection->addCategoryFilter($categoryForFilter);
                $this->collectionWithoutLimit->addCategoryFilter($categoryForFilter);
            }
        }

        if ($this->config->showPSRating()) {
            $this->reviewModel->appendSummary($this->productCollection);
        }

        return $this->productCollection;
    }

    /**
     * @return array|bool|\Magento\Catalog\Model\ResourceModel\Category\Collection|
     * \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\DataObject[]
     */
    public function getCategories()
    {
        $productCollection = $this->getProducts();
        $catIds = $this->countForCategories();

        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect("*")
            ->addFieldToFilter('entity_id', ['neq' => $rootCategoryId]);

        if (! $this->helper->versionCompare() && count($catIds) > 0) {
            arsort($catIds);
            $queryCategoryId = $this->helper->getQueryCategory();
            $useParentCat = false;

            if ($queryCategoryId && isset($catIds[$queryCategoryId])) {
                $useParentCat = true;
            }

            $catIds = array_keys($catIds);

            if ($queryCategoryId) {
                $category = $this->categoryFactory->create()->load($queryCategoryId);

                if ($category) {
                    $subCatIds = $category->getChildrenCategories()->getAllIds();
                    $catIds = array_intersect($catIds, $subCatIds);

                    if ($useParentCat) {
                       $catIds[] = $queryCategoryId;
                    }
                }
            }

            $sortedIds = "'" . str_replace(",", "','", implode(",", $catIds)) . "'";
            $categories = $categoryCollection->addFieldToFilter('entity_id', ['in' => $catIds])
                ->getSelect()->order(new \Zend_Db_Expr("FIELD(e.entity_id, " . $sortedIds . ")"));

            $categories = $categoryCollection->getItems();

            if ($limit = $this->config->getCategorySuggestionCount()) {
                $categories = array_slice($categories, 0, $limit);
            }

            return $categories;
        }

        if ($productCollection && $productCollection->getSize() > 0) {
            $productCollection->addCountToCategories($categoryCollection);

             foreach ($categoryCollection as $category) {
                if (! (bool)$category->getProductCount()) {
                    $categoryCollection->removeItemByKey($category->getId());
                }
             }

            $categories = $categoryCollection->getItems();

            usort($categories, function($a, $b) {
                return ($a->getProductCount() > $b->getProductCount())? -1 : 1;
            });

            if ($limit = $this->config->getCategorySuggestionCount()) {
                $categories = array_slice($categories, 0, $limit);
            }
        } else {
            $categories = false;
        }

        return $categories;
    }

    /**
     * @return array
     */
    public function countForCategories()
    {
        if (! empty($this->categoryIds)) {
            return $this->categoryIds;
        }

        foreach ($this->collectionWithoutLimit as $product) {
            $catIds = $product->getCategoryIds();
            foreach ($catIds as $catId) {
                if (isset($this->categoryIds[$catId])) {
                    $this->categoryIds[$catId]++;
                } else {
                    $this->categoryIds[$catId] = 1;
                }
            }
        }

        return $this->categoryIds;
    }

    /**
     * @return \Magento\Search\Model\ResourceModel\Query\Collection
     */
    public function getTerms()
    {
        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Magento\Search\Model\ResourceModel\Query\Collection $termsCollection */
        $termsCollection = $this->queryCollectionFactory->create()
            ->setPopularQueryFilter($storeId)
            ->addFieldToFilter('display_in_terms', 1)
            ->setPageSize($this->config->getTermsSuggestionCount());

        $filters = [];
        $this->queryText = $this->helper->getQueryText();

        foreach ($this->helper->getQueryWords($this->queryText) as $queryWord) {
            $filters[] = [
                'like' => $this->resourceHelper->addLikeEscape(
                    $queryWord,
                    ['position' => 'any']
                )
            ];
        }

        if ($filters) {
            if ($this->config->getLikeSeparator() === 'AND') {
                foreach ($filters as $filter) {
                    $termsCollection->addFieldToFilter('query_text', $filter);
                }
            } else {
                $termsCollection->addFieldToFilter('query_text', $filters);
            }
        }

        return $termsCollection;
    }

    /**
     * @return array
     */
    private function getAttributeToSelectList()
    {
        return [
            'name',
            'description',
            'price',
            'special_price',
            'bundle_price',
            'image',
            'tax_class_id'
        ];
    }
}
