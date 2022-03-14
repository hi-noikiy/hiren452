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

namespace Mirasvit\ProductAction\Action\CopyAttributes;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\ProductAction\Api\ActionDataInterface;
use Mirasvit\ProductAction\Api\ExecutorInterface;

class Executor implements ExecutorInterface
{
    private $attributeCollectionFactory;

    private $dataPersistor;

    private $metaProvider;

    private $productRepository;

    private $storeManager;

    public function __construct(
        MetaProvider $metaProvider,
        AttributeCollectionFactory $attributeCollectionFactory,
        DataPersistorInterface $dataPersistor,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->dataPersistor              = $dataPersistor;
        $this->metaProvider               = $metaProvider;
        $this->productRepository          = $productRepository;
        $this->storeManager               = $storeManager;
    }

    public function execute(ActionDataInterface $actionData): void
    {
        $actionData = $this->cast($actionData);

        $this->copyAttributes($actionData);
    }

    private function cast(ActionDataInterface $class): ActionData
    {
        if ($class instanceof ActionData) {
            return $class;
        }

        throw new \InvalidArgumentException((string)__('Invalid class'));
    }

    private function copyAttributes(ActionData $actionData): void
    {
        $ids            = $actionData->getIds();
        $copyFromSkus   = $actionData->getCopyFrom();
        $copyAttributes = $actionData->getCopyAttributes();

        $allowedAttributes = [];

        $collection = $this->attributeCollectionFactory->create()
            ->addFieldToFilter('is_user_defined', '1');
        foreach ($collection as $attr) {
            if (!empty($copyAttributes)) { // copy only selected attributes
                if (in_array($attr->getAttributeCode(), $copyAttributes)) {
                    $allowedAttributes[$attr->getAttributeCode()] = 1;
                }
            } else {
                $allowedAttributes[$attr->getAttributeCode()] = 1;
            }
        }

        foreach ($copyFromSkus as $sku) {
            $product = $this->productRepository->get($sku, true);

            $storeIds = $product->getStoreIds();

            array_unshift($storeIds, 0);

            foreach ($storeIds as $storeId) {
                $product = $this->productRepository->get($sku, true, $storeId);

                $fromAttributes = $product->getCustomAttributes();

                foreach ($ids as $id) {
                    /** @var Product $p */
                    $p = $this->productRepository->getById($id, true, $storeId);

                    $this->storeManager->setCurrentStore($storeId);

                    /** @var \Magento\Framework\Api\AttributeValue $fromAttribute */
                    foreach ($fromAttributes as $fromAttribute) {
                        if (isset($allowedAttributes[$fromAttribute->getAttributeCode()])) {
                            $p->setData($fromAttribute->getAttributeCode(), $product->getData($fromAttribute->getAttributeCode()));
                        }
                    }

                    $this->copyDefaultAttributes($product, $p, $copyAttributes);

                    $this->productRepository->save($p);
                }
            }
        }

        $this->dataPersistor->clear('catalog_product');
    }

    private function copyDefaultAttributes(Product $fromProduct, Product $toProduct, array $copyAttributes): void
    {
        foreach ($this->metaProvider->getDefaultAttributes() as $attribute) {
            if (!empty($copyAttributes) && !in_array($attribute['id'], $copyAttributes)) {
                continue;
            }

            $setMethodName = 'set' . $attribute['method'];
            $getMethodName = 'get' . $attribute['method'];

            $toProduct->$setMethodName($fromProduct->$getMethodName());
        }
    }
}
