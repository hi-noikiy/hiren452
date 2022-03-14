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

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Category\DataProvider;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;

/**
 * @see DataProvider::getCurrentCategory()
 */
class AddDynamicCategoryDataPlugin
{
    const FIELD_IS_DYNAMIC_CATEGORY = 'is_dynamic_category';

    private $dynamicCategoryRepository;

    public function __construct(
        DynamicCategoryRepository $dynamicCategoryRepository
    ) {
        $this->dynamicCategoryRepository = $dynamicCategoryRepository;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetCurrentCategory(DataProvider $dataProvider, Category $category): Category
    {
        $dynamicCategory = $this->dynamicCategoryRepository->getByCategoryId((int)$category->getId());

        if ($dynamicCategory) {
            $category->setData(self::FIELD_IS_DYNAMIC_CATEGORY, (string)$dynamicCategory->getIsActive());
        }

        return $category;
    }
}
