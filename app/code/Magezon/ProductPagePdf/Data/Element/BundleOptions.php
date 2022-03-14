<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductPagePdf\Data\Element;

class BundleOptions extends \Magezon\ProductPagePdf\Data\Element
{
    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
	public function prepareGeneralTab()
    {
    	$general = parent::prepareGeneralTab();

            $general->addChildren(
                'break_page',
                'toggle',
                [
                    'sortOrder'         => 5,
                    'key'               => 'break_page',
                    'defaultValue'      => false,
                    'templateOptions'   => [
                        'label'         => __('Page Break Before Element')
                    ]
                ]
            );

	        $container1 = $general->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 10
	            ]
            );
                $container1->addChildren(
                    'enable_title',
                    'toggle',
                    [
                        'sortOrder'         => 10,
                        'key'               => 'enable_title',
                        'defaultValue'      => true,
                        'templateOptions'   => [
                            'label'         => __('Enable Title')
                        ]
                    ]
                );
                $container1->addChildren(
                    'head_title',
                    'text',
                    [
                        'sortOrder'         => 20,
                        'key'               => 'head_title',
                        'defaultValue'      => "Bundle Options",
                        'templateOptions'   => [
                            'label'         => __('Title Name')
                        ],
                        'hideExpression'    => '!model.enable_title'
                    ]
                );
                $container1->addChildren(
                    'title_font_size',
                    'number',
                    [
                        'sortOrder'         => 30,
                        'key'               => 'title_font_size',
                        'defaultValue'      => 28,
                        'templateOptions'   => [
                            'label'         => __('Title Font Size')
                        ],
                        'hideExpression'    => '!model.enable_title'
                    ]
                );
                $container1->addChildren(
                    'title_color',
                    'color',
                    [
                        'sortOrder'         => 40,
                        'key'               => 'title_color',
                        'defaultValue'      => '#444444',
                        'templateOptions'   => [
                            'label'         => __('Title Color')
                        ],
                        'hideExpression'    => '!model.enable_title'
                    ]
                );

            $container2 = $general->addContainerGroup(
                'container2',
                [
                    'sortOrder' => 20
                ]
            );
                $container2->addChildren(
                    'enable_border_bottom',
                    'toggle',
                    [
                        'sortOrder'         => 10,
                        'key'               => 'enable_border_bottom',
                        'defaultValue'      => true,
                        'templateOptions'   => [
                            'label'         => __('Enable Bottom Border'),
                            'tooltip'       => __('Add border for title in bottom. Default: true'),
                            'tooltipClass'  => 'tooltip-top tooltip-top-right'
                        ],
                        'hideExpression'    => '!model.enable_title'
                    ]
                );
                $container2->addChildren(
                    'border_title_width',
                    'text',
                    [
                        'sortOrder'         => 20,
                        'key'               => 'border_title_width',
                        'defaultValue'      => 3,
                        'templateOptions'   => [
                            'label'         => __('Border Title Width')
                        ],
                        'hideExpression'    => '!model.enable_title || !model.enable_border_bottom'
                    ]
                );
                $container2->addChildren(
                    'border_title_color',
                    'color',
                    [
                        'sortOrder'         => 20,
                        'key'               => 'border_title_color',
                        'defaultValue'      => '#dddddd',
                        'templateOptions'   => [
                            'label'         => __('Border Title Color'),
                            'tooltip'       => __('Border Color. Default: #dddddd'),
                            'tooltipClass'  => 'tooltip-top tooltip-top-left'
                        ],
                        'hideExpression'    => '!model.enable_title || !model.enable_border_bottom'
                    ]
                );

            $general->addChildren(
                'enable_striped',
                'toggle',
                [
                    'sortOrder'         => 25,
                    'key'               => 'enable_striped',
                    'defaultValue'      => true,
                    'templateOptions'   => [
                        'label'         => __('Enable Stripes')
                    ]
                ]
            );

            $container3 = $general->addContainerGroup(
                'container3',
                [
                    'sortOrder' => 30   
                ]
            );
                $container3->addChildren(
                    'striped_color_dark',
                    'color',
                    [
                        'sortOrder'         => 10,
                        'key'               => 'striped_color_dark',
                        'defaultValue'      => '#d9d9d9',
                        'templateOptions'   => [
                            'label'         => __('Dark Color Stripe')
                        ],
                        'hideExpression'    => '!model.enable_striped',
                    ]
                );
                $container3->addChildren(
                    'striped_color_light',
                    'color',
                    [
                        'sortOrder'         => 10,
                        'key'               => 'striped_color_light',
                        'defaultValue'      => '#f2f2f2',
                        'templateOptions'   => [
                            'label'         => __('Light Color Stripe')
                        ],
                        'hideExpression'    => '!model.enable_striped',
                    ]
                );

            $container4 = $general->addContainerGroup(
                'container4',
                [
                    'sortOrder' => 30   
                ]
            );
                $container4->addChildren(
                    'text_font_size',
                    'number',
                    [
                        'sortOrder'         => 10,
                        'key'               => 'text_font_size',
                        'defaultValue'      => 14,
                        'templateOptions'   => [
                            'label'         => __('Text Font Size')
                        ],
                        'hideExpression'    => '!model.enable_striped',
                    ]
                );
                $container4->addChildren(
                    'text_color',
                    'color',
                    [
                        'sortOrder'         => 10,
                        'key'               => 'text_color',
                        'defaultValue'      => '#444444',
                        'templateOptions'   => [
                            'label'         => __('Text Color')
                        ],
                        'hideExpression'    => '!model.enable_striped',
                    ]
                );

        return $general;
    }
}
