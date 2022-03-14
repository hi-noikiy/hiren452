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

use Magento\Framework\App\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Modal;

trait ProductSelectorTrait
{
    use GroupTrait;

    public function elementProductSelector(string $dataScope, ?string $label, array $extra = [])
    {
        $meta = $this->elementGroup([
            'input'  => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label'         => __($label),
                            'componentType' => Form\Field::NAME,
                            'formElement'   => Form\Element\Input::NAME,
                            'dataScope'     => $dataScope,
                            'dataType'      => Form\Element\DataType\Text::NAME,
                            'component'     => 'Mirasvit_ProductAction/js/elements/product-selector',
                            'placeholder'   => 'SKU1, SKU2, SKU3',
                            'listingName'   => 'modal.' . $dataScope . '_product_listing',
                            'labelVisible'  => $label ? true : false,
                        ],
                    ],
                ],
            ],
            'button' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement'   => 'container',
                            'componentType' => 'container',
                            'component'     => 'Magento_Ui/js/form/components/button',
                            'actions'       => [
                                [
                                    'targetName'    => '${ $.parentName }.modal',
                                    'actionName'    => 'toggleModal',
                                    '__disableTmpl' => ['targetName' => false],
                                ],
                                [
                                    'targetName'    => '${ $.parentName }.modal.' . $dataScope . '_product_listing',
                                    'actionName'    => 'render',
                                    '__disableTmpl' => ['targetName' => false],
                                ],
                                [
                                    'targetName' => 'related_product_listing.related_product_listing_data_source',
                                    'actionName' => 'reload',
                                ],
                            ],
                            'title'         => 'Choose',
                            'provider'      => null,
                        ],
                    ],
                ],
            ],
            'modal'  => $this->getGenericModal('Select Products', $dataScope),
        ], $extra);

        $meta['arguments']['data']['config']['additionalClasses'] .= ' admin__field mst-product-action__element-product-selector';

        return $meta;
    }

    protected function getGenericModal(string $title, string $scope): array
    {
        $urlManager = ObjectManager::getInstance()->create(UrlInterface::class);

        $listingTarget = $scope . '_product_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope'     => '',
                        'options'       => [
                            'title'   => $title,
                            'buttons' => [
                                [
                                    'text'    => __('Cancel'),
                                    'actions' => [
                                        'closeModal',
                                    ],
                                ],
                                [
                                    'text'    => __('Add Selected Products'),
                                    'class'   => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save',
                                        ],
                                        'closeModal',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children'  => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender'         => false,
                                'componentType'      => 'insertListing',
                                'dataScope'          => $listingTarget,
                                'selectionsProvider' => 'related_product_listing.related_product_listing.product_columns.ids',
                                'ns'                 => $listingTarget,
                                'render_url'         => $urlManager->getUrl('mui/index/render', ['namespace' => 'related_product_listing']),
                                'realTimeLink'       => false,
                                'dataLinks'          => [
                                    'imports' => false,
                                    'exports' => true,
                                ],
                                'behaviourType'      => 'simple',
                                'externalFilterMode' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
