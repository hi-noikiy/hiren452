<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Model\Indexer;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Repository\IndexRepository;
use Mirasvit\ProductKit\Repository\KitItemRepository;

class KitIndexer
{
    private $kitItemRepository;

    private $indexRepository;

    private $productCollectionFactory;

    public function __construct(
        KitItemRepository $kitItemRepository,
        IndexRepository $indexRepository,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->kitItemRepository        = $kitItemRepository;
        $this->indexRepository          = $indexRepository;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function reindex(KitInterface $kit)
    {
        $this->indexRepository->delete($kit);

        $items = $this->kitItemRepository->getItems($kit);

        foreach ($items as $item) {
            if ($kit->isSmart() === false) {
                $row = $this->kitItemRepository->create();

                $row->setKitId($kit->getId())
                    ->setId($item->getId())
                    ->setProductId($item->getProductId())
                    ->setPosition($item->getPosition())
                    ->setIsOptional($item->isOptional())
                    ->setIsPrimary($item->isPrimary());

                $this->indexRepository->insertRow($kit, $row);

            } else {
                $collection = $this->productCollectionFactory->create();
                $ids        = $item->getRule()->getMatchedProductIds($collection);

                foreach ($ids as $productId) {
                    $row = $this->kitItemRepository->create();

                    $row->setKitId($kit->getId())
                        ->setId($item->getId())
                        ->setProductId($productId)
                        ->setPosition($item->getPosition())
                        ->setIsOptional($item->isOptional())
                        ->setIsPrimary($item->isPrimary());

                    $this->indexRepository->insertRow($kit, $row);
                }
            }
        }

        $this->indexRepository->commit();
    }
}
