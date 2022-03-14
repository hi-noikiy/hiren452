<?php
/**
 *
 * Copyright © 2013-2018 commercepundit, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace BT\Jewellery\Model\ResourceModel\Jewellery\Grid;

use BT\Jewellery\Model\ResourceModel\Jewellery\Collection as ContactCollection;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Collection extends ContactCollection implements SearchResultInterface
{
    /**
     * @var
     */
    protected $aggregations;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param string $resourceModel
     * @param string $model
     * @param null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_init($model, $resourceModel);
        $this->getMainTable();
    }

    /**
     * This Function is getAggregations
     * @return mixed
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * This Function is setAggregations
     * @param \Magento\Framework\Api\Search\AggregationInterface $aggregations
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * This Function is getSearchCriteria
     * @return null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * This Function is setSearchCriteria
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $searchCriteria;
    }

    /**
     * This Function is getTotalCount
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * This Function is setTotalCount
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $totalCount;
    }

    /**
     * This Function is setItems
     * @param array|null $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $items;
    }
}
