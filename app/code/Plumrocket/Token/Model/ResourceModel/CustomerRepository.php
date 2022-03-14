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
 * @package     Plumrocket_Token
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Token\Model\ResourceModel;

use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\ValidatorException;
use Plumrocket\Token\Api\CustomerRepositoryInterface;
use Plumrocket\Token\Api\Data\CustomerInterface as TokenDataInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * @var \Plumrocket\Token\Api\Data\CustomerInterface[]
     */
    private $instancesById = [];

    /**
     * @var \Plumrocket\Token\Api\Data\CustomerInterface[]
     */
    private $instancesByHash = [];

    /**
     * @var \Plumrocket\Token\Model\CustomerFactory
     */
    private $tokenFactory;

    /**
     * @var \Plumrocket\Token\Model\ResourceModel\Customer
     */
    private $resourceModel;

    /**
     * @var \Plumrocket\Token\Model\ResourceModel\Security\Token\GetTokenIdByHash
     */
    private $getTokenIdByHash;

    /**
     * @var \Plumrocket\Token\Api\Data\CustomerSearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var \Plumrocket\Token\Model\ResourceModel\Customer\CollectionFactory
     */
    private $tokenCollectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Plumrocket\Token\Model\Magento\VersionProvider
     */
    private $magentoVersionProvider;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * CustomerRepository constructor.
     *
     * @param \Plumrocket\Token\Model\CustomerFactory                          $tokenFactory
     * @param \Plumrocket\Token\Model\ResourceModel\Customer                   $resourceModel
     * @param \Plumrocket\Token\Model\ResourceModel\Customer\GetTokenIdByHash  $getTokenIdByHash
     * @param \Plumrocket\Token\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultFactory
     * @param \Plumrocket\Token\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                     $searchCriteriaBuilder
     * @param \Plumrocket\Token\Model\Magento\VersionProvider                  $magentoVersionProvider
     * @param \Magento\Framework\ObjectManagerInterface                        $objectManager
     */
    public function __construct(
        \Plumrocket\Token\Model\CustomerFactory $tokenFactory,
        \Plumrocket\Token\Model\ResourceModel\Customer $resourceModel,
        \Plumrocket\Token\Model\ResourceModel\Customer\GetTokenIdByHash $getTokenIdByHash,
        \Plumrocket\Token\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultFactory,
        \Plumrocket\Token\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Plumrocket\Token\Model\Magento\VersionProvider $magentoVersionProvider,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->tokenFactory = $tokenFactory;
        $this->resourceModel = $resourceModel;
        $this->getTokenIdByHash = $getTokenIdByHash;
        $this->searchResultFactory = $searchResultFactory;
        $this->tokenCollectionFactory = $collectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->magentoVersionProvider = $magentoVersionProvider;
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritDoc
     */
    public function save(\Plumrocket\Token\Api\Data\CustomerInterface $token) : TokenDataInterface
    {
        try {
            unset($this->instancesById[$token->getId()], $this->instancesByHash[$token->getHash()]);
            $this->resourceModel->save($token);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('The checkbox was unable to be saved. Please try again.'),
                $e
            );
        }

        unset($this->instancesById[$token->getId()], $this->instancesByHash[$token->getHash()]);
        return $this->getById($token->getId());
    }

    /**
     * Get info about token by token hash
     *
     * @param string $tokenHash
     * @param bool   $forceReload
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $tokenHash, bool $forceReload = false) : TokenDataInterface
    {
        if (! $tokenHash) {
            throw new NoSuchEntityException(
                __("The token that was requested doesn't exist. Verify the token and try again.")
            );
        }

        if (! isset($this->instancesByHash[$tokenHash]) || $forceReload) {
            $this->getById($this->getTokenIdByHash->execute($tokenHash));
        }

        return $this->instancesByHash[$tokenHash];
    }

    /**
     * Get info about token by token id
     *
     * @param int  $tokenId
     * @param bool $forceReload
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $tokenId, bool $forceReload = false) : TokenDataInterface
    {
        if (! $tokenId) {
            throw new NoSuchEntityException(
                __("The token that was requested doesn't exist. Verify the token and try again.")
            );
        }

        if (! isset($this->instancesById[$tokenId]) || $forceReload) {
            /** @var \Plumrocket\Token\Model\Customer|\Plumrocket\Token\Api\Data\CustomerInterface $token */
            $token = $this->tokenFactory->create();
            $token->load($tokenId);
            if (! $token->getId()) {
                throw new NoSuchEntityException(
                    __("The token that was requested doesn't exist. Verify the token and try again.")
                );
            }

            $this->instancesById[$token->getId()] = $token;
            $this->instancesByHash[$token->getHash()] = $token;
        }

        return $this->instancesById[$tokenId];
    }

    /**
     * @param \Plumrocket\Token\Api\Data\CustomerInterface $token
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(\Plumrocket\Token\Api\Data\CustomerInterface $token) : bool
    {
        $tokenId = $token->getId();
        $tokenHash = $token->getHash();
        try {
            unset($this->instancesById[$tokenId], $this->instancesByHash[$tokenHash]);
            $this->resourceModel->delete($token);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('The "%1" token couldn\'t be removed.', $tokenId)
            );
        }

        unset($this->instancesById[$tokenId], $this->instancesByHash[$tokenHash]);

        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById(int $id) : bool
    {
        $token = $this->getById($id);
        return $this->delete($token);
    }

    /**
     * Get token list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Plumrocket\Token\Api\Data\CustomerSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null) : SearchResultsInterface
    {
        /** @var \Plumrocket\Token\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->tokenCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else if ($this->magentoVersionProvider->isMagentoVersionBelow('2.2.0')) {
            $this->process($searchCriteria, $collection);
        } else {
            /** @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor */
            $collectionProcessor = $this->objectManager->get('\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface');
            $collectionProcessor->process($searchCriteria, $collection);
        }

        /** @var \Plumrocket\Token\Api\Data\CustomerSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }

    private function process(SearchCriteriaInterface $searchCriteria, Customer\Collection $collection): CustomerRepositoryInterface
    {
        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);
        return $this;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface            $searchCriteria
     * @param \Plumrocket\Token\Model\ResourceModel\Customer\Collection $collection
     * @return $this
     */
    public function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, Customer\Collection $collection
    ): CustomerRepositoryInterface {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];

            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }

            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface            $searchCriteria
     * @param \Plumrocket\Token\Model\ResourceModel\Customer\Collection $collection
     * @return $this
     */
    public function addSortOrdersToCollection(
        SearchCriteriaInterface $searchCriteria,
        Customer\Collection $collection
    ): CustomerRepositoryInterface {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() === SortOrder::SORT_ASC ? SortOrder::SORT_ASC : SortOrder::SORT_DESC;
            $collection->addOrder($sortOrder->getField(), $direction);
        }
        return $this;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface            $searchCriteria
     * @param \Plumrocket\Token\Model\ResourceModel\Customer\Collection $collection
     * @return $this
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, Customer\Collection $collection): CustomerRepositoryInterface
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
        return $this;
    }
}
