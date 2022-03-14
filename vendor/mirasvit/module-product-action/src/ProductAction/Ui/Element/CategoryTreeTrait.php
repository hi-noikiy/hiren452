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

trait CategoryTreeTrait
{
    public function getCategoryTree(string $dataScope, ?string $label, array $options, array $extra = [])
    {
        return array_replace_recursive([
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'            => __($label),
                        'formElement'      => 'select',
                        'componentType'    => 'field',
                        'component'        => 'Magento_Catalog/js/components/new-category',
                        'filterOptions'    => true,
                        'chipsEnabled'     => true,
                        'disableLabel'     => true,
                        'levelsVisibility' => '1',
                        'labelVisible'     => $label ? true : false,
                        'disabled'         => false,
                        'options'          => $options,
                        'elementTmpl'      => 'ui/grid/filters/elements/ui-select',
                        'dataScope'        => $dataScope,
                    ],
                ],
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
