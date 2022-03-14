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

namespace Mirasvit\ProductAction\Action\UpdatePrice;

use Mirasvit\ProductAction\Api\MetaProviderInterface;
use Mirasvit\ProductAction\Ui\Element;

class MetaProvider implements MetaProviderInterface
{
    const PARAM_IS_PRICE    = 'is_update_price';
    const PARAM_PRICE_VALUE = 'price_value';

    const PARAM_IS_COST    = 'is_update_cost';
    const PARAM_COST_VALUE = 'cost_value';

    const PARAM_IS_SPECIAL          = 'is_update_special_price';
    const PARAM_SPECIAL_PRICE_VALUE = 'special_price_value';
    const PARAM_SPECIAL_PRICE_FROM  = 'special_from_date';
    const PARAM_SPECIAL_PRICE_TO    = 'special_to_date';

    use Element\DateTrait;
    use Element\EnqueueTrait;
    use Element\TextTrait;
    use Element\ToggleGroupTrait;

    public function getMeta(): array
    {
        return [
            $this->getEnqueue(),

            $this->elementToggleGroup(
                $this->elementToggle(self::PARAM_IS_PRICE, 'Update Price', [
                    'default' => 1,
                ]),
                [
                    $this->elementText(self::PARAM_PRICE_VALUE, 'Update Value', [
                        'placeholder' => '10.2, +10.2, -10.2, +10.2%, -10.2%',
                    ]),
                ]
            ),

            $this->elementToggleGroup(
                $this->elementToggle(self::PARAM_IS_COST, 'Update Cost'),
                [
                    $this->elementText(self::PARAM_COST_VALUE, 'Update Value', [
                        'placeholder' => '10.2, +10.2, -10.2, +10.2%, -10.2%',
                    ]),
                ]
            ),

            $this->elementToggleGroup(
                $this->elementToggle(self::PARAM_IS_SPECIAL, 'Update Special Price'),
                [
                    $this->elementText(self::PARAM_SPECIAL_PRICE_VALUE, 'Update Value', [
                        'placeholder' => '10.2, +10.2, -10.2, +10.2%, -10.2%',
                    ]),

                    $this->getDateField('Special Price From', self::PARAM_SPECIAL_PRICE_FROM),

                    $this->getDateField('Special Price To', self::PARAM_SPECIAL_PRICE_TO),
                ],
                [],
                [
                    'additionalClasses' => 'mst-product-action__element_block-group',
                ]
            ),
        ];
    }
}
