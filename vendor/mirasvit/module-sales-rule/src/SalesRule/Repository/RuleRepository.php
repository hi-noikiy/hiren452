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
 * @package   mirasvit/module-sales-rule
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SalesRule\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\SalesRule\Api\Data\RuleInterface;
use Mirasvit\SalesRule\Api\Data\RuleInterfaceFactory;
use Mirasvit\SalesRule\Api\Repository\RuleRepositoryInterface;
use Mirasvit\SalesRule\Model\ResourceModel\Rule\CollectionFactory;

class RuleRepository implements RuleRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var RuleInterfaceFactory 
     */
    private $factory;

    /**
     * RuleRepository constructor.
     * @param EntityManager $entityManager
     * @param CollectionFactory $collectionFactory
     * @param RuleInterfaceFactory $factory
     */
    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        RuleInterfaceFactory $factory
    ) {
        $this->entityManager     = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->factory           = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * {@inheritdoc}
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
     * @param int $id
     * @return bool|false|RuleInterface|\Mirasvit\SalesRule\Model\Rule
     */
    public function getByParentId($id)
    {
        /** @var \Mirasvit\SalesRule\Model\Rule $rule */
        $rule = $this->create();
        $rule = $rule->load($id, RuleInterface::PARENT_ID);

        return $rule->getId() ? $rule : false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(RuleInterface $model)
    {
        $this->entityManager->delete($model);
    }

    /**
     * {@inheritdoc}
     */
    public function save(RuleInterface $model)
    {
        return $this->entityManager->save($model);
    }
}