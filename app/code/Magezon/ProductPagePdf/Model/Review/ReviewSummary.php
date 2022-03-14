<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */
declare(strict_types=1);

namespace Magezon\ProductPagePdf\Model\Review;

use Magento\Framework\Model\AbstractModel;
use Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory as SummaryCollectionFactory;

class ReviewSummary
{
    /**
     * @var SummaryCollectionFactory
     */
    private $summaryCollectionFactory;

    /**
     * @param SummaryCollectionFactory $sumColFactory
     */
    public function __construct(
        SummaryCollectionFactory $sumColFactory
    ) {
        $this->summaryCollectionFactory = $sumColFactory;
    }

    /**
     * Append review summary data to product
     *
     * @param AbstractModel $object
     * @param int $storeId
     * @param int $entityType
     */
    public function appendSummaryDataToObject(AbstractModel $object, int $storeId, int $entityType = 1): void
    {
        $summary = $this->summaryCollectionFactory->create()
            ->addEntityFilter($object->getId(), $entityType)
            ->addStoreFilter($storeId)
            ->getFirstItem();
        $object->addData(
            [
                'reviews_count' => $summary->getData('reviews_count'),
                'rating_summary' => $summary->getData('rating_summary')
            ]
        );
    }
}
