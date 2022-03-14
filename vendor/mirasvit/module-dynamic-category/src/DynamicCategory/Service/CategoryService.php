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

namespace Mirasvit\DynamicCategory\Service;

use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Model\StoreManager;

class CategoryService
{
    private $groupRepository;

    private $storeManager;

    private $rootCategoryIds = [];

    public function __construct(
        GroupRepositoryInterface $groupRepository,
        StoreManager $storeManager
    ) {
        $this->groupRepository = $groupRepository;
        $this->storeManager    = $storeManager;
    }

    public function getRootCategoryIds(): array
    {
        if (!$this->rootCategoryIds) {
            $stores = $this->storeManager->getStores();
            foreach ($stores as $store) {
                $storeGroup = $this->groupRepository->get($store->getStoreGroupId());

                $this->rootCategoryIds[$storeGroup->getRootCategoryId()] = $storeGroup->getDefaultStoreId();
            }
        }

        return $this->rootCategoryIds;
    }
}
