<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Facebook
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
namespace Magedelight\Facebook\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magedelight\Facebook\Api\Data;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magedelight\Facebook\Api\Data\AttributemapSearchResultsInterfaceFactory;
use Magedelight\Facebook\Api\AttributemapRepositoryInterface;
use Magedelight\Facebook\Model\ResourceModel\Attributemap as AttributemapResourceModel;
use Magedelight\Facebook\Model\ResourceModel\Attributemap\Collection;
use Magedelight\Facebook\Model\ResourceModel\Attributemap\CollectionFactory;

/**
 * Attributemap repository
 */
class AttributemapRepository implements AttributemapRepositoryInterface
{
    /**
     * @var AttributemapResourceModel
     */
    protected $resourceModel;

    /**
     * @var AttributemapFactory
    */
    protected $attributemapFactory;

    /**
     * @var AttributemapSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * 
     * @param AttributemapResourceModel $resourceModel
     * @param \Magedelight\Facebook\Model\AttributemapFactory $attributemapFactory
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AttributemapSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        AttributemapResourceModel $resourceModel,
        AttributemapFactory $attributemapFactory,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AttributemapSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->resourceModel = $resourceModel;
        $this->attributemapFactory = $attributemapFactory;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
    }

    /**
     * Lists attribute mapping that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     * @return \Magedelight\Facebook\Api\Data\AttributemapSearchResultsInterfaceFactory Attribute mapping search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        
        /** @var \Magedelight\Facebook\Model\ResourceModel\AttributemapFactory\Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        /** @var \Magedelight\Facebook\Api\Data\AttributemapSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * Loads a specified attribute mapping.
     *
     * @param int $entityId The attribute mapping entity ID.
     * @return \Magedelight\Facebook\Api\Data\AttributemapInterface Attribute Mapping interface.
     */
    public function getById($entityId)
    {
        $attributeMapModel = $this->attributemapFactory->create();
        $this->resourceModel->load($attributeMapModel, $entityId);
        return $attributeMapModel;
    }

    /**
     * Delete Attribute Map
     *
     * @param \Magedelight\Facebook\Api\Data\AttributemapInterface $attributemap
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\Magedelight\Facebook\Api\Data\AttributemapInterface $attributemap)
    {
        try {
            $this->resourceModel->delete($attributemap);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the attribute map: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete Attribute Mapping by given Page Identity
     *
     * @param string $attributemapid
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($attributemapid)
    {
        return $this->delete($this->getById($attributemapid));
    }
    
    /**
     * Performs persist operations for a specified attribute mapping.
     *
     * @param \Magedelight\Facebook\Api\Data\AttributemapInterface $attributemap The attribute map.
     * @return \Magedelight\Facebook\Api\Data\AttributemapInterface Saved attribute mapping data.
     */
    public function save(Data\AttributemapInterface $attributemap)
    {
        try {
            $this->resourceModel->save($attributemap);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the page: %1', $exception->getMessage()),
                $exception
            );
        }
        return $attributemap;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     * @deprecated 100.3.0
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Retrieve collection processor
     *
     * @deprecated 100.3.0
     * @return CollectionProcessorInterface
     */
    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface::class
            );
        }
        return $this->collectionProcessor;
    }
}
