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
 * @package   mirasvit/module-dynamic-category
 * @version   1.0.17
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



declare(strict_types=1);

namespace Mirasvit\DynamicCategory\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\DynamicCategory\Api\Data\DynamicCategoryInterface;
use Mirasvit\DynamicCategory\Api\Data\DynamicCategoryInterfaceFactory;
use Mirasvit\DynamicCategory\Model\ResourceModel\DynamicCategory\Collection;
use Mirasvit\DynamicCategory\Model\ResourceModel\DynamicCategory\CollectionFactory;

class DynamicCategoryRepository
{
    private $entityManager;

    private $collectionFactory;

    private $factory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        DynamicCategoryInterfaceFactory $factory
    ) {
        $this->entityManager     = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->factory           = $factory;
    }

    /**
     * @return DynamicCategoryInterface[]|Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    public function create(): DynamicCategoryInterface
    {
        return $this->factory->create();
    }

    public function get(int $id): ?DynamicCategoryInterface
    {
        $model = $this->create();
        $model = $this->entityManager->load($model, $id);

        return $model->getId() ? $model : null;
    }

    public function getByCategoryId(int $categoryId): ?DynamicCategoryInterface
    {
        $model = $this->getCollection()
            ->addFieldToFilter(DynamicCategoryInterface::CATEGORY_ID, $categoryId)
            ->getFirstItem();

        return $model->getId() ? $model : null;
    }

    public function save(DynamicCategoryInterface $model): DynamicCategoryInterface
    {
        return $this->entityManager->save($model);
    }

    public function delete(DynamicCategoryInterface $model): void
    {
        $this->entityManager->delete($model);
    }
}
