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

namespace Mirasvit\DynamicCategory\Plugin\Backend;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Mirasvit\DynamicCategory\Api\Data\DynamicCategoryInterface;
use Mirasvit\DynamicCategory\Registry;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;

/**
 * @see CategoryLinkManagementInterface::assignProductToCategories()
 */
class AddDynamicCategoryOnProductSavePlugin
{
    const FIELD_IS_DYNAMIC_CATEGORY = 'is_dynamic_category';

    private $collectionFactory;

    private $dynamicCategoryRepository;

    private $registry;

    public function __construct(
        CollectionFactory $collectionFactory,
        DynamicCategoryRepository $dynamicCategoryRepository,
        Registry $registry
    ) {
        $this->collectionFactory         = $collectionFactory;
        $this->dynamicCategoryRepository = $dynamicCategoryRepository;
        $this->registry                  = $registry;
    }

    /**
     * @param CategoryLinkManagementInterface $subject
     * @param string $productSku
     * @param array  $categoryIds
     *
     * @return array
     */
    public function beforeAssignProductToCategories(CategoryLinkManagementInterface $subject, string $productSku, array $categoryIds): array
    {
        $collection = $this->dynamicCategoryRepository->getCollection()
            ->addFieldToFilter(DynamicCategoryInterface::IS_ACTIVE, 1);

        /** @var DynamicCategoryInterface $dynamicCategory */
        foreach ($collection as $dynamicCategory) {
            $this->registry->setCurrentDynamicCategory($dynamicCategory);

            $productCollection = $this->collectionFactory->create();
            $productCollection->addFieldToFilter(ProductInterface::SKU, $productSku);

            $dynamicCategory->getRule()->applyToCollection($productCollection);

            if ($productCollection->count() > 0) {
                $categoryIds[] = $dynamicCategory->getCategoryId();
            }

            $this->registry->setCurrentDynamicCategory(null);
        }

        return [$productSku, $categoryIds];
    }
}
