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
use Magento\Framework\Controller\Result\Raw;
use Mirasvit\DynamicCategory\Registry;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;
use Mirasvit\DynamicCategory\Ui\Category\Form\Block\Conditions;

class Load extends Action implements HttpPostActionInterface
{
    private $categoryFactory;

    private $conditions;

    private $dynamicCategoryRepository;

    private $registry;

    public function __construct(
        CategoryFactory $categoryFactory,
        Conditions $conditions,
        DynamicCategoryRepository $dynamicCategoryRepository,
        Registry $registry,
        Action\Context $context
    ) {
        parent::__construct($context);

        $this->categoryFactory = $categoryFactory;
        $this->conditions      = $conditions;

        $this->dynamicCategoryRepository = $dynamicCategoryRepository;

        $this->registry = $registry;
    }

    public function execute(): Raw
    {
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultFactory->create('raw');

        $id = (int)$this->getRequest()->getParam('category_id');

        $dynamicCategory = $this->dynamicCategoryRepository->getByCategoryId($id);

        if (!$dynamicCategory) {
            $resultRaw->setContents('');

            return $resultRaw;
        }

        $category = $this->categoryFactory->create()->load($id);

        $this->registry->setCategory($category);
        $this->registry->setCurrentCategory($category);
        $this->registry->setCurrentDynamicCategory($dynamicCategory);

        $resultRaw->setContents($this->conditions->toHtml());

        return $resultRaw;
    }
}
