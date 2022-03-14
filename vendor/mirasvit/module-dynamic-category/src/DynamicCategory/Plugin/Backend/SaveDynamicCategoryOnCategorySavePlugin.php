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
use Magento\Catalog\Model\ResourceModel\Category as ResourceCategory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\DynamicCategory\Model\DynamicCategory;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;
use Mirasvit\DynamicCategory\Service\CategoryService;

/**
 * @see \Magento\Catalog\Model\ResourceModel\Category::_afterSave
 * @see \Magento\Catalog\Model\ResourceModel\Category::_saveCategoryProducts
 */
class SaveDynamicCategoryOnCategorySavePlugin
{
    private $categoryService;

    private $collectionFactory;

    private $dynamicCategoryRepository;

    private $messageManager;

    public function __construct(
        CategoryService $categoryService,
        CollectionFactory $collectionFactory,
        DynamicCategoryRepository $dynamicCategoryRepository,
        ManagerInterface $messageManager
    ) {
        $this->categoryService           = $categoryService;
        $this->collectionFactory         = $collectionFactory;
        $this->dynamicCategoryRepository = $dynamicCategoryRepository;
        $this->messageManager            = $messageManager;
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function aroundSave(object $subject, callable $proceed, AbstractModel $category): ResourceCategory
    {
        /** @var Category $category */
        if (!$category->hasData(AddDynamicCategoryDataPlugin::FIELD_IS_DYNAMIC_CATEGORY)) {
            return $proceed($category);
        }

        $id       = (int)$category->getId();
        $isActive = (bool)$category->getData(AddDynamicCategoryDataPlugin::FIELD_IS_DYNAMIC_CATEGORY);
        $ruleData = $category->getData('rule');

        $path = explode('/', $category->getPath());

        $rootCategoryIds = $this->categoryService->getRootCategoryIds();

        $storeId = 0;
        foreach ($rootCategoryIds as $rootCategoryId => $categoryStoreId) {
            if (in_array($rootCategoryId, $path)) {
                $storeId = $categoryStoreId;
                break;
            }
        }

        $dynamicCategory = $this->getDynamicCategory($id);

        $dynamicCategory->setCategoryId($id);
        $dynamicCategory->setIsActive($isActive);

        if ($isActive) {
            $rule = $dynamicCategory->getRule();

            if ($ruleData) {
                $conditions = $rule->loadPost($ruleData)->getConditions()->asArray();
                $conditions = (string)SerializeService::encode($conditions);

                $dynamicCategory->setConditionsSerialized($conditions);
            }

            $productCollection = $this->collectionFactory->create()->addStoreFilter($storeId);

            try {
                $ids = $dynamicCategory->getRule()->getMatchingProductIds($productCollection);

                $categoryProductIds = array_fill_keys($ids, 0);
                $productIds         = (array)$category->getData('posted_products');

                $commonIds          = array_intersect_key($productIds, $categoryProductIds);
                $categoryProductIds = $commonIds + $categoryProductIds;

                // set dynamic products instead of selected by user
                $category->setData('posted_products', $categoryProductIds);
            } catch (LocalizedException $e) {
                $this->messageManager->addWarningMessage($e->getMessage());
            }
        }

        $return = $proceed($category);

        if (!$category->getId()) {
            return $return;
        }

        $dynamicCategory->setCategoryId((int)$category->getId());

        $this->dynamicCategoryRepository->save($dynamicCategory);

        return $return;
    }

    private function getDynamicCategory(int $id): DynamicCategory
    {
        if (!$id) {
            $dynamicCategory = $this->dynamicCategoryRepository->create();
        } else {
            $dynamicCategory = $this->dynamicCategoryRepository->getByCategoryId($id);

            if (!$dynamicCategory) {
                $dynamicCategory = $this->dynamicCategoryRepository->create();
            }
        }

        return $dynamicCategory;
    }

}
