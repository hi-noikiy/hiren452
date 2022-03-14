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

namespace Magezon\ProductPagePdf\Model\ResourceModel\Profile\Relation\Store;

use Magezon\ProductPagePdf\Model\ResourceModel\Profile;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var Profile
     */
    protected $profileResource;

    /**
     * @param Profile $profileResource
     */
    public function __construct(
        Profile $profileResource
    ) {
        $this->profileResource = $profileResource;
    }

    /**
     * @param \Magezon\ProductPagePdf\Model\Profile $entity
     * @param array $arguments
     * @return \Magezon\ProductPagePdf\Model\Profile
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $storeIds = $this->profileResource->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $storeIds);
        }
        return $entity;
    }
}
