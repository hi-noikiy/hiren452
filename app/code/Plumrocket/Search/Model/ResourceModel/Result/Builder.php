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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Search\Model\ResourceModel\Result;

use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Magento\Framework\DB\Select;

class Builder extends  \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var
     */
    private $queryText;

    /**
     * @var
     */
    private $searchResult;

    /**\
     * @var \Magento\Search\Api\SearchInterface
     */
    private $searchInterface;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory
     */
    private $temporaryStorageFactory;

    /**
     * @var null
     */
    private $relevanceOrderDirection = null;

    /**
     * SearchBuilder constructor.
     *
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder             $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder                            $filterBuilder
     * @param \Magento\Framework\Data\Collection\EntityFactory                $entityFactory
     * @param \Magento\Search\Api\SearchInterface                             $searchInterface
     * @param \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory
     * @param \Psr\Log\LoggerInterface                                        $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface    $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                       $eventManager
     * @param \Magento\Eav\Model\Config                                       $eavConfig
     * @param \Magento\Framework\App\ResourceConnection                       $resource
     * @param \Magento\Eav\Model\EntityFactory                                $eavEntityFactory
     * @param \Magento\Catalog\Model\ResourceModel\Helper                     $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory                   $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Magento\Framework\Module\Manager                               $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State               $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface              $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory                    $productOptionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Url                        $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface            $localeDate
     * @param \Magento\Customer\Model\Session                                 $customerSession
     * @param \Magento\Framework\Stdlib\DateTime                              $dateTime
     * @param \Magento\Customer\Api\GroupManagementInterface                  $groupManagement
     */
    public function __construct(
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Magento\Search\Api\SearchInterface $searchInterface,
        \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement
        );

        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->searchInterface = $searchInterface;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
    }

    /**
     * @param $query
     * @return $this
     */
    public function addSearchFilter($query)
    {
        $this->queryText = trim($this->queryText . ' ' . $query);
        $this->applySearch();

        return $this;
    }

    /**
     * @param string $dir
     */
    public function setRelevanceOrderDirection($dir = Select::SQL_DESC)
    {
        $this->relevanceOrderDirection = $dir;
    }

    /**
     * void
     */
    public function applySearch()
    {
        if ($this->queryText) {
            $this->filterBuilder->setField('search_term');
            $this->filterBuilder->setValue($this->queryText);
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setRequestName('quick_search_container');

        $this->searchResult = $this->searchInterface->search($searchCriteria);

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $table = $temporaryStorage->storeApiDocuments($this->searchResult->getItems());

        try {
            $this->getSelect()->joinInner(
                [
                    'search_result' => $table->getName(),
                ],
                'e.entity_id = search_result.' . TemporaryStorage::FIELD_ENTITY_ID,
                []
            );
        } catch (\Exception $e) {

        }

        if ($this->relevanceOrderDirection) {
            $this->getSelect()->order(
                'search_result.'. TemporaryStorage::FIELD_SCORE . ' ' . $this->relevanceOrderDirection
            );
        }
    }
}