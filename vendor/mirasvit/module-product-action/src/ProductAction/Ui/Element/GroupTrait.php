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

trait GroupTrait
{
    public function elementGroup(array $children, array $extra = [])
    {
        return array_replace_recursive([
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement'       => 'container',
                        'componentType'     => 'container',
                        'label'             => false,
                        'component'         => 'Mirasvit_ProductAction/js/elements/group',
                        'template'          => 'Mirasvit_ProductAction/elements/group',
                        'additionalClasses' => 'mst-product-action__element-group',
                        'visible'           => true,
                    ],
                ],
            ],
            'children'  => $children,
        ], [
            'arguments' => [
                'data' => [
                    'config' => $extra,
                ],
            ],
        ]);
    }
}
