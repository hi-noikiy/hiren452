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

class Price extends \Magezon\ProductPagePdf\Data\Element
{
    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
	public function prepareGeneralTab()
    {
    	$general = parent::prepareGeneralTab();

            $container1 = $general->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 10
	            ]
            );
                $container1->addChildren(
		            'break_page',
                    'toggle',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'break_page',
                        'templateOptions' => [
                            'label' => __('Page Break Before Element')
                        ]
                    ]
                );

            $container2 = $general->addContainerGroup(
                    'container2',
                    [
                        'sortOrder' => 20
                    ]
                );
                $container2->addChildren(
                    'diplay_label',
                    'toggle',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'diplay_label',
                        'templateOptions' => [
                            'label' => __('Enabel Label')
                        ]
                    ]
                );
                $container2->addChildren(
                    'label_font_size',
                    'number',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'label_font_size',
                        'defaultValue'    => 24,
                        'templateOptions' => [
                            'label' => __('Label Font Size')
                        ],
                        'hideExpression' => '!model.diplay_label'
                    ]
                );
                $container2->addChildren(
                    'label_color',
                    'color',
                    [
                        'sortOrder'       => 30,
                        'key'             => 'label_color',
                        'defaultValue'    => '#333333',
                        'templateOptions' => [
                            'label' => __('Label Color')
                        ],
                        'hideExpression' => '!model.diplay_label'
                    ]
                );

            $container3 = $general->addContainerGroup(
                'container3',
                [
                    'sortOrder' => 30
                ]
            );
                $container3->addChildren(
                    'price_font_size',
                    'number',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'price_font_size',
                        'defaultValue'    => 28,
                        'templateOptions' => [
                            'label' => __('Price Font Size')
                        ]
                    ]
                );
                $container3->addChildren(
                    'price_line_height',
                    'number',
                    [
                        'sortOrder'       => 40,
                        'key'             => 'price_line_height',
                        'defaultValue'    => 30,
                        'templateOptions' => [
                            'label' => __('Price Line Height')
                        ]
                    ]
                );
                $container3->addChildren(
                    'price_color',
                    'color',
                    [
                        'sortOrder'       => 40,
                        'key'             => 'price_color',
                        'defaultValue'    => '#333333',
                        'templateOptions' => [
                            'label' => __('Price Color')
                        ]
                    ]
                );

        return $general;
    }
}
