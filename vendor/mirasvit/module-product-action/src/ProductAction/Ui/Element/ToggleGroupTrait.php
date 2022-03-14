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

namespace Mirasvit\ProductAction\Ui\Element;

trait ToggleGroupTrait
{
    use GroupTrait;

    public function elementToggleGroup(array $toggle, array $children, array $extra = [], array $groupExtra = [])
    {
        return array_replace_recursive([
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement'       => 'container',
                        'componentType'     => 'container',
                        'component'         => 'Mirasvit_ProductAction/js/elements/group',
                        'template'          => 'Mirasvit_ProductAction/elements/group',
                        'additionalClasses' => 'mst-product-action__element-toggle-group',
                        'visible'           => true,
                    ],
                ],
            ],
            'children'  => [
                'switch' => $toggle,
                'group'  => $this->elementGroup($children, array_replace_recursive([
                    'imports' => [
                        'visible'       => '${ $.provider }:data.' . $toggle['arguments']['data']['config']['dataScope'],
                        '__disableTmpl' => ['visible' => false],
                    ],
                ], $groupExtra)),
            ],
        ], [
            'arguments' => [
                'data' => [
                    'config' => $extra,
                ],
            ],
        ]);
    }
}
