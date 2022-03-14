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

namespace Mirasvit\ProductAction\Action\UpdateGallery;

use Mirasvit\ProductAction\Api\MetaProviderInterface;
use Mirasvit\ProductAction\Ui\Element;

class MetaProvider implements MetaProviderInterface
{
    const PARAM_IS_REMOVE  = 'is_remove';
    const PARAM_IS_COPY    = 'is_copy';
    const PARAM_PARENT_SKU = 'media_copy_from';

    use Element\ProductSelectorTrait;
    use Element\EnqueueTrait;

    public function getMeta(): array
    {
        return [
            $this->elementToggle(self::PARAM_IS_REMOVE, 'Remove Images', [
                'default' => 0,
                'notice'  => 'Remove ALL images for selected product(s)',
            ]),

            $this->elementToggle(self::PARAM_IS_COPY, 'Copy Gallery Images', [
                'default' => 1,
                'notice'  => 'Copy images from below product(s) to selected product(s)',
            ]),

            $this->elementProductSelector(self::PARAM_PARENT_SKU, 'Copy from', [
                'imports' => [
                    'visible'       => '${ $.provider }:data.' . self::PARAM_IS_COPY,
                    '__disableTmpl' => ['visible' => false],
                ],
            ]),

            $this->getEnqueue(),
        ];
    }
}
