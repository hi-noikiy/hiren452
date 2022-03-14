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



namespace Mirasvit\ProductKit\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Api\Data\KitInterfaceFactory;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Model\ResourceModel\Kit\CollectionFactory;

class KitRepository
{
    private $entityManager;

    private $collectionFactory;

    private $factory;

    private $kitItemRepository;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        KitInterfaceFactory $factory,
        KitItemRepository $kitItemRepository
    ) {
        $this->entityManager     = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->factory           = $factory;
        $this->kitItemRepository = $kitItemRepository;
    }

    /**
     * @return KitInterface[]|\Mirasvit\ProductKit\Model\ResourceModel\Kit\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return KitInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @param int $id
     *
     * @return KitInterface|false
     */
    public function get($id)
    {
        $model = $this->create();
        $model = $this->entityManager->load($model, $id);

        if (!$model->getId()) {
            return false;
        }

        return $model;
    }

    /**
     * @param KitInterface $model
     *
     * @return KitInterface
     */
    public function save(KitInterface $model)
    {
        return $this->entityManager->save($model);
    }

    /**
     * @param KitInterface $model
     */
    public function delete(KitInterface $model)
    {
        $this->entityManager->delete($model);
    }

    /**
     * @return KitItemRepository
     */
    public function getItemRepository()
    {
        return $this->kitItemRepository;
    }

    /**
     * @param KitInterface       $kit
     * @param KitItemInterface[] $items
     *
     * @return KitInterface
     */
    public function saveItems(KitInterface $kit, $items)
    {
        $itemRepository = $this->getItemRepository();

        $kitItems = $itemRepository->getItems($kit);
        foreach ($kitItems as $item) {
            $itemRepository->delete($item);
        }

        $result = [];
        foreach ($items as $item) {
            $itemRepository->save($item);
            $result[$item->getPosition()] = $item;
        }

        return $kit;
    }
}
