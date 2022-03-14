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



namespace Mirasvit\ProductKit\Plugin;

use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Model\Indexer;
use Mirasvit\ProductKit\Repository\KitRepository;

/**
 * @see \Mirasvit\ProductKit\Repository\KitRepository::save()
 */
class IndexerPlugin
{
    private $indexer;

    public function __construct(
        Indexer $indexer
    ) {
        $this->indexer = $indexer;
    }

    /**
     * @param KitRepository $subject
     * @param KitInterface  $kit
     * @return KitInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave($subject, KitInterface $kit)
    {
        if (!$kit->isSmart()) {
            // reindex on fly
            $this->indexer->executeRow($kit->getId());
        }

        return $kit;
    }

    /**
     * @param KitRepository $subject
     * @param KitInterface  $kit
     * @return KitInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSaveItems($subject, KitInterface $kit)
    {
        if (!$kit->isSmart()) {
            // reindex on fly
            $this->indexer->executeRow($kit->getId());
        }

        return $kit;
    }
}