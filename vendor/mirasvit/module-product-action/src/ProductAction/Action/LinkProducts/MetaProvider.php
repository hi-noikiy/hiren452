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

namespace Mirasvit\ProductAction\Action\LinkProducts;

use Mirasvit\ProductAction\Api\LinkActionDataInterface;
use Mirasvit\ProductAction\Api\MetaProviderInterface;
use Mirasvit\ProductAction\Registry;
use Mirasvit\ProductAction\Ui\Element;

class MetaProvider implements MetaProviderInterface
{
    const PARAM_IS_ADD     = 'mst_is_add';
    const PARAM_ADD        = 'mst_add';
    const PARAM_DIRECTION  = 'mst_direction';
    const PARAM_IS_REMOVE  = 'mst_is_remove';
    const PARAM_REMOVE     = 'mst_remove';
    const PARAM_REMOVE_ALL = 'mst_remove_all';
    const PARAM_IS_COPY    = 'mst_is_copy';
    const PARAM_COPY       = 'mst_copy';
    const PARAM_LINK_TYPE  = 'mst_link_type';

    use Element\ProductSelectorTrait;
    use Element\EnqueueTrait;

    private $label;

    public function __construct(
        string $label = ''
    ) {
        $this->label = $label;
    }

    public function getMeta(): array
    {
        return [
            $this->elementGroup([
                self::PARAM_IS_ADD => $this->elementToggle(self::PARAM_IS_ADD, __('Add ' . $this->label . ' Products')->getText(), [
                    'default' => 1,
                ]),

                self::PARAM_ADD => $this->elementProductSelector(self::PARAM_ADD, null, [
                    'imports' => [
                        'visible'       => '${ $.provider }:data.' . self::PARAM_IS_ADD,
                        '__disableTmpl' => ['visible' => false],
                    ],
                    'additionalClasses' => 'mst-product-action-hide-label',
                ]),
            ]),

            $this->elementGroup([
                self::PARAM_IS_REMOVE => $this->elementToggle(self::PARAM_IS_REMOVE, __('Remove ' . $this->label . ' Products')->getText()),
                self::PARAM_REMOVE    => $this->elementProductSelector(self::PARAM_REMOVE, null, [
                    'imports' => [
                        'visible'       => '${ $.provider }:data.' . self::PARAM_IS_REMOVE,
                        '__disableTmpl' => ['visible' => false],
                    ],
                    'additionalClasses' => 'mst-product-action-hide-label',
                ]),
            ]),

            $this->elementGroup([
                self::PARAM_IS_COPY => $this->elementToggle(self::PARAM_IS_COPY, __('Copy ' . $this->label . ' Products')->getText()),
                self::PARAM_COPY    => $this->elementProductSelector(self::PARAM_COPY, null, [
                    'imports' => [
                        'visible'       => '${ $.provider }:data.' . self::PARAM_IS_COPY,
                        '__disableTmpl' => ['visible' => false],
                    ],
                    'additionalClasses' => 'mst-product-action-hide-label',
                ]),
            ]),

            $this->elementToggle(
                self::PARAM_DIRECTION,
                __('Apply in both directions')->getText(),
                [
                    'notice' => __('Link products to each other')->getText(),
                ]
            ),

            $this->elementToggle(self::PARAM_REMOVE_ALL, __('Remove all products')->getText(), []),

            $this->getEnqueue(),
        ];
    }
}
