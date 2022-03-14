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



namespace Mirasvit\DynamicCategory\Ui\Category\Source;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Option\ArrayInterface;
use Mirasvit\DynamicCategory\Api\Data\DynamicCategoryInterface;
use Mirasvit\DynamicCategory\Registry;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;

class CategorySource implements ArrayInterface
{
    private $categoryFactory;

    private $dynamicCategoryRepository;

    private $registry;

    public function __construct(
        CategoryFactory $categoryFactory,
        DynamicCategoryRepository $dynamicCategoryRepository,
        Registry $registry
    ) {
        $this->categoryFactory           = $categoryFactory;
        $this->dynamicCategoryRepository = $dynamicCategoryRepository;
        $this->registry                  = $registry;
    }

    public function toOptionArray(): array
    {
        return $this->getSuggestedCategoriesJson();
    }

    public function getSuggestedCategoriesJson(): array
    {
        /* @var $collection Collection */
        $collection = $this->categoryFactory->create()->getCollection();

        $dynamicCategories = $this->dynamicCategoryRepository->getCollection();

        $matchingNamesCollection = clone $collection;
        $matchingNamesCollection->addAttributeToFilter(
            'entity_id',
            ['in' => $dynamicCategories->getColumnValues(DynamicCategoryInterface::CATEGORY_ID)]
        )->addAttributeToSelect(
            ['name', 'is_active', 'parent_id']
        );

        $shownCategoriesIds = [];
        foreach ($matchingNamesCollection as $category) {
            foreach (explode('/', $category->getPath()) as $parentId) {
                $shownCategoriesIds[$parentId] = 1;
            }
        }

        $collection->addAttributeToFilter(
            'entity_id',
            ['in' => array_keys($shownCategoriesIds)]
        )->addAttributeToSelect(
            ['name', 'is_active', 'parent_id']
        );

        $categoryById = [
            \Magento\Catalog\Model\Category::TREE_ROOT_ID => [
                'id'       => \Magento\Catalog\Model\Category::TREE_ROOT_ID,
                'optgroup' => [],
            ],
        ];
        foreach ($collection as $category) {
            foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = ['value' => $categoryId, 'optgroup' => []];
                }
            }

            $categoryById[$category->getId()]['label'] = $category->getName();
            $categoryById[$category->getId()]['value'] = $category->getId();

            $categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$category->getId()];
        }

        return $categoryById[\Magento\Catalog\Model\Category::TREE_ROOT_ID]['optgroup'];
    }
}
