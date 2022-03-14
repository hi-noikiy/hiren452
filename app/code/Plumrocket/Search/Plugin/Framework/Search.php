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

namespace Plumrocket\Search\Plugin\Framework;

use Magento\Framework\Search\SearchEngineInterface;
use Magento\Framework\Search\SearchResponseBuilder;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Search\Request\Builder;
use Magento\Catalog\Model\Product;
use Plumrocket\Search\Helper\Search as PrsearchHelper;

class Search
{
    /**
     * @var Builder
     */
    private $requestBuilder;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var SearchEngineInterface
     */
    private $searchEngine;

    /**
     * @var SearchResponseBuilder
     */
    private $searchResponseBuilder;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var string
     */
    private $currentSearchTerm;

    /**
     * @var PrsearchHelper
     */
    private $searchHelper;

    /**
     * Search constructor.
     *
     * @param Builder                $requestBuilder
     * @param ScopeResolverInterface $scopeResolver
     * @param SearchEngineInterface  $searchEngine
     * @param SearchResponseBuilder  $searchResponseBuilder
     * @param Product                $product
     * @param PrsearchHelper         $searchHelper
     */
    public function __construct(
        Builder $requestBuilder,
        ScopeResolverInterface $scopeResolver,
        SearchEngineInterface $searchEngine,
        SearchResponseBuilder $searchResponseBuilder,
        Product $product,
        PrsearchHelper $searchHelper
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->scopeResolver = $scopeResolver;
        $this->searchEngine = $searchEngine;
        $this->searchResponseBuilder = $searchResponseBuilder;
        $this->product = $product;
        $this->searchHelper = $searchHelper;
    }

    /**
     * @param \Magento\Framework\Search\Search $subject
     * @param \Closure                         $proceed
     * @param SearchCriteriaInterface          $searchCriteria
     * @return \Magento\Framework\Api\Search\SearchResultInterface
     */
    public function aroundSearch(
        \Magento\Framework\Search\Search $subject,
        \Closure $proceed,
        SearchCriteriaInterface $searchCriteria
    ) {
        if (! $this->searchHelper->moduleEnabled() || ! $this->searchHelper->allowedLogic()) {
            return $proceed($searchCriteria);
        }

        $this->requestBuilder->setRequestName($searchCriteria->getRequestName());

        $scope = $this->scopeResolver->getScope()->getId();
        $this->requestBuilder->bindDimension('scope', $scope);

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() !== PrsearchHelper::SEARCH_TERMS) {
                    $this->addFieldToFilter($filter->getField(), $filter->getValue());
                } else if ($filter->getField() === PrsearchHelper::SEARCH_TERMS) {
                    $this->currentSearchTerm = $filter->getValue();

                    if ($this->searchHelper->likeSeparator() === PrsearchHelper::CONDITION_OR) {
                        $terms = explode(' ', $filter->getValue());
                        foreach ($terms as $term) {
                            $this->addFieldToFilter(PrsearchHelper::SEARCH_TERMS, trim($term));
                        }
                    } else if ($this->searchHelper->likeSeparator() === PrsearchHelper::CONDITION_AND) {
                        $this->addFieldToFilter(PrsearchHelper::SEARCH_TERMS, $this->currentSearchTerm);
                    }
                }
            }
        }

        $this->requestBuilder->setFrom($searchCriteria->getCurrentPage() * $searchCriteria->getPageSize());
        $this->requestBuilder->setSize($searchCriteria->getPageSize());
        $request = $this->requestBuilder->create();

        $searchResponse = $this->searchEngine->search($request);
        $results = $this->searchResponseBuilder->build($searchResponse)
            ->setSearchCriteria($searchCriteria);

        $searchResponseData = $results->getItems();

        $obj = [];

        foreach ($searchResponseData as $item) {
            $obj[$item->getId()] = $item;
        }

        if (! count($obj)) {
            return $results;
        }

        $sortedIds = $this->searchHelper->sortCollection($this->getProductCollection(array_keys($obj)));

        $res = [];

        foreach ($sortedIds as $key => $value) {
            if (! empty($obj[$value])) {
                $res[] = $obj[$value];
            }
        }

        return $results->setItems($res);
    }

    /**
     * @param $productIds
     * @return mixed
     */
    private function getProductCollection($productIds)
    {
        return $this->product->getCollection()
            ->addAttributeToSelect($this->searchHelper->getAttributes())
            ->addAttributeToFilter('entity_id', $productIds);
    }

    /**
     * @param      $field
     * @param null $condition
     * @return $this
     */
    private function addFieldToFilter($field, $condition = null)
    {
        if (!is_array($condition) || !in_array(key($condition), ['from', 'to'], true)) {
            $this->requestBuilder->bind($field, $condition);
        } else {
            if (!empty($condition['from'])) {
                $this->requestBuilder->bind("{$field}.from", $condition['from']);
            }
            if (!empty($condition['to'])) {
                $this->requestBuilder->bind("{$field}.to", $condition['to']);
            }
        }

        return $this;
    }
}