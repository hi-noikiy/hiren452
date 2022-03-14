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

use Magento\Ui\Component\Form;

trait CheckboxTrait
{
    public function getCheckbox(string $label, string $dataScope, array $extra = [])
    {
        return array_replace_recursive([
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __($label),
                        'componentType' => Form\Field::NAME,
                        'formElement'   => Form\Element\Checkbox::NAME,
                        'dataScope'     => $dataScope,
                        'dataType'      => Form\Element\DataType\Boolean::NAME,
                        'valueMap'      => [
                            'true' => true,
                        ],
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
