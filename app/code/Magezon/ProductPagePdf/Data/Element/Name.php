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

class Name extends \Magezon\ProductPagePdf\Data\Element
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
                    'sortOrder'       => 5,
                    'key'             => 'break_page',
                    'defaultValue'    => false,
                    'templateOptions' => [
                        'label' => __('Page Break Before Element')
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
                    'heading_type',
                    'select',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'heading_type',
                        'defaultValue'    => 'h1',
                        'templateOptions' => [
                            'label'   => __('Heading Type'),
                            'options' => $this->getHeadingType()
                        ]
                    ]
                );
                $container1->addChildren(
                    'font_size',
                    'number',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'font_size',
                        'defaultValue'    => 28,
                        'templateOptions' => [
                            'label' => __('Font size')
                        ]
                    ]
                );
                $container1->addChildren(
                    'text_color',
                    'color',
                    [
                        'key'             => 'text_color',
                        'sortOrder'       => 30,
                        'defaultValue'    => '#333333',
                        'templateOptions' => [
                            'label' => __('Text Color')
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
                    'line_height',
                    'number',
                    [
                        'sortOrder'       => 40,
                        'key'             => 'line_height',
                        'defaultValue'    => 30,
                        'templateOptions' => [
                            'label' => __('Line height')
                        ]
                    ]
                );
                $container2->addChildren(
                    'font_weight',
                    'number',
                    [
                        'sortOrder'       => 50,
                        'key'             => 'font_weight',
                        'defaultValue'    => 400,
                        'templateOptions' => [
                            'label' => __('Font Weight')
                        ]
                    ]
                );

        return $general;
    }
}
