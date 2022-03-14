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

namespace Magezon\ProductPagePdf\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Profile extends AbstractDb
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param EntityManager $entityManager
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        EntityManager $entityManager
    ) {
        parent::__construct($context);
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
    }

    protected function _construct()
    {
        $this->_init('mgz_productpagepdf_profile', 'profile_id');
    }

    /**
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['cbs' => $this->getTable('mgz_productpagepdf_profile_store')], 'store_id')
            ->join(
                ['cb' => $this->getMainTable()],
                'cbs.profile_id = cb.profile_id',
                []
            )
            ->where('cb.profile_id = :profile_id');

        return $connection->fetchCol($select, ['profile_id' => (int)$id]);
    }

    /**
     * @param AbstractModel $object
     * @return \Magezon\ProductPagePdf\Model\Profile
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * Load the object
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param null $field
     * @return \Magezon\ProductPagePdf\Model\Profile
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $this->entityManager->load($object, $value);
        return $this;
    }
}
