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

namespace Mirasvit\DynamicCategory\Ui\DataProvider\Product\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Mirasvit\DynamicCategory\Api\Data\DynamicCategoryInterface;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;

class DynamicCategory implements ModifierInterface
{
    private $arrayManager;

    private $dynamicCategoryRepository;

    public function __construct(
        ArrayManager $arrayManager,
        DynamicCategoryRepository $dynamicCategoryRepository
    ) {
        $this->arrayManager = $arrayManager;

        $this->dynamicCategoryRepository = $dynamicCategoryRepository;
    }

    /**
     * @inheritdoc
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        $fieldCode = 'category_ids';
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $data = $this->arrayManager->get($elementPath, $meta);

        if (!empty($data['arguments']['data']['config']['options'])) {
            $collection = $this->dynamicCategoryRepository->getCollection()
                ->addFieldToFilter(DynamicCategoryInterface::IS_ACTIVE, true);

            if ($collection->count()) {
                $ids = $collection->getColumnValues(DynamicCategoryInterface::CATEGORY_ID);

                $this->modifyCategories($data['arguments']['data']['config']['options'], $ids);


                $meta = $this->arrayManager->merge($elementPath, $meta, $data);
            }
        }

        return $meta;
    }

    private function modifyCategories(array &$data, array $ids)
    {
        foreach ($data as $i => $category) {
            if (in_array($category['value'], $ids)) {
                $data[$i]['label'] = __('Dynamic Category: ') . $data[$i]['label'];
            }

            if (!empty($category['optgroup'])) {
                $this->modifyCategories($data[$i]['optgroup'], $ids);
            }
        }
    }
}
