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

class Review extends \Magezon\ProductPagePdf\Data\Element
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
                        'defaultValue'      => "Reviews",
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
                        'defaultValue'      => '#333333',
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
                            'label'         => __('Enable Border'),
                            'tooltip'       => __('Add border for title in bottom. Default: true'),
                            'tooltipClass'  => 'tooltip-top tooltip-top-right'
                        ],
                        'hideExpression'    => '!model.enable_title'
                    ]
                );
                $container2->addChildren(
                    'border_title_width',
                    'number',
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
                        'sortOrder'         => 30,
                        'key'               => 'border_title_color',
                        'defaultValue'      => '#dddddd',
                        'templateOptions'   => [
                            'label'         => __('Border Title Color'),
                            'tooltip'       => __('Border Color. Default: #dddddd'),
                            'tooltipClass'  => 'tooltip-top tooltip-top-right'
                        ],
                        'hideExpression'    => '!model.enable_title || !model.enable_border_bottom'
                    ]
                );
            
            $container3 = $general->addContainerGroup(
                'container3',
                [
                    'sortOrder' => 30
                ]
            ); 
                $container3->addChildren(
                    'display_type',
                    'select',
                    [
                        'sortOrder'       => 10,
						'key'             => 'display_type',
						'defaultValue'    => 'type1',
						'templateOptions' => [
							'label'   => __('Display Type'),
							'options' => $this->getDisplayType()
	                    ]
                    ]
                );
                $container3->addChildren(
                    'max_review_count',
                    'number',
                    [
                        'sortOrder'       => 20,
						'key'             => 'max_review_count',
						'defaultValue'    => 3,
						'templateOptions' => [
							'label'   => __('Max Review Count')
	                    ]
                    ]
                );
            
            $container4 = $general->addContainerGroup(
                'container4',
                [
                    'sortOrder' => 40
                ]
            ); 
                $container4->addChildren(
                    'color_style',
                    'color',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'color_style',
                        'defaultValue'    => '#454545',
                        'templateOptions' => [
                            'label'   => __('Color Style')
                        ]
                    ]
                );
                $container4->addChildren(
                    'content_font_size',
                    'color',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'content_font_size',
                        'defaultValue'    => '14',
                        'templateOptions' => [
                            'label'   => __('Content Font Size')
                        ]
                    ]
                );

    	return $general;
    }

    public function getDisplayType() 
    {
        return [
            [
                'label' => 'Type 1',
                'value' => 'type1'
            ],
            [
                'label' => 'Type 2',
                'value' => 'type2'
            ]
        ];
    }
}