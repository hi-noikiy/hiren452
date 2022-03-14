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



namespace Mirasvit\ProductKit\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Repository\KitRepository;

class Indexer implements IndexerActionInterface, MviewActionInterface, IdentityInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'mst_product_kit';

    private $kitRepository;

    private $kitIndexer;

    private $eventManager;

    public function __construct(
        KitRepository $kitRepository,
        Indexer\KitIndexer $kitIndexer,
        ManagerInterface $eventManager
    ) {
        $this->kitRepository = $kitRepository;
        $this->kitIndexer    = $kitIndexer;
        $this->eventManager  = $eventManager;
    }

    public function getIdentities()
    {
        return [
            \Magento\Catalog\Model\Product::CACHE_TAG,
        ];
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     *
     * @return void
     */
    public function execute($ids)
    {
        $collection = $this->kitRepository->getCollection();

        if ($ids) {
            $collection->addFieldToFilter(KitInterface::ID, $ids);
        }

        foreach ($collection as $kit) {
            $this->kitIndexer->reindex($kit);
        }

        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
    }

    /**
     * Execute full indexation
     *
     * @param array $ids
     *
     * @return void
     */
    public function executeFull(array $ids = [])
    {
        $this->execute($ids);
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     *
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     *
     * @return void
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }
}