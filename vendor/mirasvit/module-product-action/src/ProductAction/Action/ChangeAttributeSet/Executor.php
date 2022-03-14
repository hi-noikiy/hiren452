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
 * @package   mirasvit/module-product-action
 * @version   1.0.9
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\ProductAction\Action\ChangeAttributeSet;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Mirasvit\ProductAction\Api\ActionDataInterface;
use Mirasvit\ProductAction\Api\ExecutorInterface;

class Executor implements ExecutorInterface
{
    const FIELD_ATTRIBUTE_SET_ID = 'attribute_set_id';

    private $actionDataFactory;

    private $connection;

    private $metadataPool;

    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resource,
        ActionDataFactory $actionDataFactory
    ) {
        $this->connection   = $resource->getConnection();
        $this->metadataPool = $metadataPool;

        $this->actionDataFactory = $actionDataFactory;
    }

    /**
     * @param ActionDataInterface $actionData
     *
     * @throws \Zend_Json_Exception
     */
    public function execute(ActionDataInterface $actionData): void
    {
        $this->updateAttributeSet($this->cast($actionData));
    }

    private function updateAttributeSet(ActionData $actionData): void
    {
        $entityMetadata = $this->metadataPool->getMetadata(ProductInterface::class);

        $parts = array_chunk($actionData->getIds(), self::PRODUCTS_PER_INSERT);
        foreach ($parts as $part) {
            $this->connection->update(
                $entityMetadata->getEntityTable(),
                [ProductInterface::ATTRIBUTE_SET_ID => $actionData->getAttributeSetId()],
                [$entityMetadata->getIdentifierField() . ' IN (?)' => $part]
            );
        }
    }

    private function cast(ActionDataInterface $class): ActionData
    {
        if ($class instanceof ActionData) {
            return $class;
        }

        throw new \InvalidArgumentException((string)__('Invalid class'));
    }
}
