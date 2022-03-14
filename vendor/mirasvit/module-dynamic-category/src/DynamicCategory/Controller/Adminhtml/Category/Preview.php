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

namespace Mirasvit\DynamicCategory\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\Result\Raw;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\DynamicCategory\Registry;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;

class Preview extends Action implements HttpPostActionInterface
{
    private $categoryFactory;

    private $dynamicCategoryRepository;

    private $layoutFactory;

    private $registry;

    public function __construct(
        CategoryFactory $categoryFactory,
        DynamicCategoryRepository $dynamicCategoryRepository,
        LayoutFactory $layoutFactory,
        Registry $registry,
        Action\Context $context
    ) {
        parent::__construct($context);

        $this->categoryFactory = $categoryFactory;

        $this->dynamicCategoryRepository = $dynamicCategoryRepository;

        $this->layoutFactory = $layoutFactory;
        $this->registry      = $registry;
    }

    public function execute(): Raw
    {
        $id       = (int)$this->getRequest()->getParam('categoryId');
        $isActive = (bool)$this->getRequest()->getParam('is_dynamic_category');

        $dynamicCategory = $this->dynamicCategoryRepository->getByCategoryId($id);

        if (!$dynamicCategory) {
            $dynamicCategory = $this->dynamicCategoryRepository->create();
        }

        $rule = $dynamicCategory->getRule();

        $conditions = $rule->loadPost($this->getRequest()->getParam('rule'))->getConditions()->asArray();
        $conditions = (string)SerializeService::encode($conditions);

        $dynamicCategory->setCategoryId($id);
        $dynamicCategory->setConditionsSerialized($conditions);
        $dynamicCategory->setIsActive($isActive);

        /** @var Raw $resultRaw */
        $resultRaw = $this->resultFactory->create('raw');

        $category = $this->categoryFactory->create()->load($id);

        $category->setId(null);
        $this->registry->setCategory($category);
        $this->registry->setCurrentDynamicCategory($dynamicCategory);

        /** @var \Mirasvit\DynamicCategory\Block\Adminhtml\Category\Tab\Product $gridBlock */
        $gridBlock = $this->layoutFactory->create()->createBlock(
            \Mirasvit\DynamicCategory\Block\Adminhtml\Category\Tab\Product::class,
            'category.product.grid'
        );

        $this->registry->setIsGetSizeResetGroup(true);

        $html = $gridBlock->toHtml();

        $this->registry->setIsGetSizeResetGroup(false);

        $resultRaw->setContents($html);

        $this->registry->setCurrentPreviewDynamicCategory(null);

        return $resultRaw;
    }
}
