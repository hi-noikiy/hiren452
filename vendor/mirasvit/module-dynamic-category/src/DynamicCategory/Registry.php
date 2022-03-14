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

namespace Mirasvit\DynamicCategory;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Registry as CoreRegistry;
use Mirasvit\DynamicCategory\Api\Data\DynamicCategoryInterface;

class Registry
{
    private $coreRegistry;

    private $currentDynamicCategory;

    private $currentPreviewDynamicCategory;

    private $getSizeResetGroup;

    public function __construct(
        CoreRegistry $coreRegistry
    ) {
        $this->coreRegistry = $coreRegistry;
    }

    public function getCurrentCategory(): ?CategoryInterface
    {
        return $this->coreRegistry->registry('current_category');
    }

    public function setCurrentCategory(?CategoryInterface $category): void
    {
        $this->coreRegistry->unregister('current_category');
        $this->coreRegistry->register('current_category', $category);
    }

    public function getCategory(): ?CategoryInterface
    {
        return $this->coreRegistry->registry('category');
    }

    public function setCategory(?CategoryInterface $category): void
    {
        $this->coreRegistry->unregister('category');
        $this->coreRegistry->register('category', $category);
    }

    public function getCurrentDynamicCategory(): ?DynamicCategoryInterface
    {
        return $this->currentDynamicCategory;
    }

    public function setCurrentDynamicCategory(?DynamicCategoryInterface $dynamicCategory): void
    {
        $this->currentDynamicCategory = $dynamicCategory;
    }

    public function getCurrentPreviewDynamicCategory(): ?DynamicCategoryInterface
    {
        return $this->currentPreviewDynamicCategory;
    }

    public function setCurrentPreviewDynamicCategory(?DynamicCategoryInterface $dynamicCategory): void
    {
        $this->currentPreviewDynamicCategory = $dynamicCategory;
    }

    public function getIsGetSizeResetGroup(): ?bool
    {
        return $this->getSizeResetGroup;
    }

    public function setIsGetSizeResetGroup(?bool $flag): void
    {
        $this->getSizeResetGroup = $flag;
    }
}
