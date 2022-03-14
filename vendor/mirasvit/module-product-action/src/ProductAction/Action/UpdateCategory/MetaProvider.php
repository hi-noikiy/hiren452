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
 * @package   mirasvit/module-product-action
 * @version   1.0.9
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\ProductAction\Action\UpdateCategory;

use Mirasvit\ProductAction\Api\MetaProviderInterface;
use Mirasvit\ProductAction\Ui\Action\Source\CategoryTreeSource;
use Mirasvit\ProductAction\Ui\Element;

class MetaProvider implements MetaProviderInterface
{
    const PARAM_IS_REMOVE_CATEGORY = 'is_remove_category';
    const PARAM_IS_ADD_CATEGORY    = 'is_add_category';

    const PARAM_REMOVE_CATEGORY = 'remove_category';
    const PARAM_ADD_CATEGORY    = 'add_category';

    use Element\CategoryTreeTrait;
    use Element\CheckboxTrait;
    use Element\GroupTrait;
    use Element\EnqueueTrait;

    private $categorySource;

    public function __construct(
        CategoryTreeSource $categorySource
    ) {
        $this->categorySource = $categorySource;
    }

    public function getMeta(): array
    {
        return [
            $this->elementGroup([
                self::PARAM_IS_ADD_CATEGORY => $this->elementToggle(
                    self::PARAM_IS_ADD_CATEGORY,
                    'Add Categories',
                    [
                        'default' => 1,
                    ]
                ),
                self::PARAM_ADD_CATEGORY    => $this->getCategoryTree(
                    self::PARAM_ADD_CATEGORY,
                    null,
                    $this->categorySource->toOptionArray(),
                    [
                        'imports' => [
                            'visible'       => '${ $.provider }:data.' . self::PARAM_IS_ADD_CATEGORY,
                            '__disableTmpl' => ['visible' => false],
                        ],
                        'additionalClasses' => 'mst-product-action-hide-label',
                    ]
                ),
            ]),

            $this->elementGroup([
                self::PARAM_IS_REMOVE_CATEGORY => $this->elementToggle(
                    self::PARAM_IS_REMOVE_CATEGORY,
                    'Remove Categories'
                ),
                self::PARAM_REMOVE_CATEGORY    => $this->getCategoryTree(
                    self::PARAM_REMOVE_CATEGORY,
                    null,
                    $this->categorySource->toOptionArray(),
                    [
                        'imports' => [
                            'visible'       => '${ $.provider }:data.' . self::PARAM_IS_REMOVE_CATEGORY,
                            '__disableTmpl' => ['visible' => false],
                        ],
                        'additionalClasses' => 'mst-product-action-hide-label',
                    ]
                ),
            ]),

            $this->getEnqueue(),
        ];
    }
}
