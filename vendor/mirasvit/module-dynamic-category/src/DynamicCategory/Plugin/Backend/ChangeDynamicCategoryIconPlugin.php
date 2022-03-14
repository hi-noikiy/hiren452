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

use Magento\Catalog\Block\Adminhtml\Category\Tree;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\DynamicCategory\Api\Data\DynamicCategoryInterface;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;

/**
 * @see Tree::getTreeJson()
 */
class ChangeDynamicCategoryIconPlugin
{
    private $dynamicCategoryRepository;

    public function __construct(
        DynamicCategoryRepository $dynamicCategoryRepository
    ) {
        $this->dynamicCategoryRepository = $dynamicCategoryRepository;
    }

    /**
     * @param Tree $subject
     * @param string $json
     *
     * @return string
     */
    public function afterGetTreeJson(Tree $subject, string $json): string
    {
        $collection = $this->dynamicCategoryRepository->getCollection()
            ->addFieldToFilter(DynamicCategoryInterface::IS_ACTIVE, true);

        if ($collection->count()) {
            $ids = $collection->getColumnValues(DynamicCategoryInterface::CATEGORY_ID);

            $data = SerializeService::decode($json);

            $data = $this->updateDynamicCategory($data, $ids);

            $json = SerializeService::encode($data);
        }

        return $json;
    }

    private function updateDynamicCategory(array &$data, array $ids): array
    {
        foreach ($data as $i => $category) {
            if (in_array($category['id'], $ids)) {
                $data[$i]['cls'] .= ' mst-dynamic-category-icon';
            }

            if (!empty($category['children'])) {
                $this->updateDynamicCategory($data[$i]['children'], $ids);
            }
        }

        return $data;
    }
}
