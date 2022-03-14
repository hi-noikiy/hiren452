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
 * @package   Plumrocket_AutoInvoiceShipment
 * @copyright Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AutoInvoiceShipment\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class SourceItem
 *
 * @package Plumrocket\AutoInvoiceShipment\Model
 */
class SourceItem
{
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * SourceItem constructor.
     * @param ObjectProvider $objectProvider
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaInterface $searchCriteria
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ObjectProvider $objectProvider,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaInterface $searchCriteria,
        StoreManagerInterface $storeManager
    ) {
        $this->objectProvider = $objectProvider;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteria = $searchCriteria;
        $this->storeManager = $storeManager;
    }

    /**
     *  Return product qty by source
     *
     * @param ProductInterface $product
     * @param int|null $websiteId
     * @return int|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductQty(ProductInterface $product, int $websiteId = null)
    {
        $qty = 0;
        $filterGroups = [];
        $sourceCode = $this->getSourceCode($websiteId);

        if (! $sourceCode) {
            return null;
        }

        $sourceItemRepository = $this->objectProvider
            ->create(\Magento\InventoryApi\Api\SourceItemRepositoryInterface::class);

        $skuFilter = $this->filterBuilder
            ->setField('sku')
            ->setValue($product->getSku())
            ->setConditionType('eq')
            ->create();

        $skuFilterGroup =  $this->filterGroupBuilder->setFilters([$skuFilter])->create();

        $sourceCodeFilter = $this->filterBuilder
            ->setField('source_code')
            ->setValue($sourceCode)
            ->setConditionType('eq')
            ->create();

        $sourceCodeFilterGroup = $this->filterGroupBuilder->setFilters([$sourceCodeFilter])->create();
        $filterGroups = [$sourceCodeFilterGroup, $skuFilterGroup];
        $searchCriteria = $this->searchCriteria->setFilterGroups($filterGroups);

        foreach ($sourceItemRepository->getList($searchCriteria)->getItems() as $item) {
            $qty += $item->getQuantity();
        }

        return $qty;
    }

    /**
     * @param int|null $websiteId
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSourceCode(int $websiteId = null)
    {
        if (null === $websiteId) {
            $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        }

        $stockId = $this->objectProvider
            ->get(\Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface::class)
            ->execute($websiteId)
            ->getStockId();

        $sources = $this->objectProvider
            ->get(\Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface::class)
            ->execute((int)$stockId);

        //TODO: rebuild this logic in future magento version
        if (! empty($sources) && count($sources) == 1) {
            $sourceCode = $sources[0]->getSourceCode();
        } else {
            $sourceCode = $this->objectProvider
                ->get(\Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface::class)
                ->getCode();
        }

        return $sourceCode;
    }
}
