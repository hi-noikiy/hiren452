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
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterfaceFactory;
use Mirasvit\ProductKit\Model\ResourceModel\KitItem\CollectionFactory;

class KitItemRepository
{
    private $entityManager;

    private $collectionFactory;

    private $factory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        KitItemInterfaceFactory $factory
    ) {
        $this->entityManager     = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->factory           = $factory;
    }

    /**
     * @return KitItemInterface[]|\Mirasvit\ProductKit\Model\ResourceModel\KitItem\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return KitItemInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @param int $id
     *
     * @return KitItemInterface|false
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
     * @param KitItemInterface $model
     *
     * @return KitItemInterface
     */
    public function save(KitItemInterface $model)
    {
        return $this->entityManager->save($model);
    }

    /**
     * @param KitItemInterface $model
     */
    public function delete(KitItemInterface $model)
    {
        $this->entityManager->delete($model);
    }

    /**
     * @param KitInterface $kit
     *
     * @return KitItemInterface[]
     */
    public function getItems(KitInterface $kit)
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter(KitItemInterface::KIT_ID, (int)$kit->getId())
            ->setOrder(KitItemInterface::POSITION, 'asc');

        $result = [];
        foreach ($collection as $item) {
            $result[$item->getPosition()] = $item;
        }

        return $result;
    }
}
