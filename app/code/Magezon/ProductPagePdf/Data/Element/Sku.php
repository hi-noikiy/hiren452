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

class SKU extends \Magezon\ProductPagePdf\Data\Element
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
                    'font_size',
                    'number',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'font_size',
                        'defaultValue'    => 20,
                        'templateOptions' => [
                            'label' => __('Font size')
                        ]
                    ]
                );

                $container2->addChildren(
                    'line_height',
                    'number',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'line_height',
                        'defaultValue'    => 22,
                        'templateOptions' => [
                            'label' => __('Line height')
                        ]
                    ]
                );

                $container2->addChildren(
                    'text_color',
                    'color',
                    [
                        'sortOrder'       => 40,
                        'key'             => 'text_color',
                        'defaultValue'    => '#333333',
                        'templateOptions' => [
                            'label' => __('Text Color')
                        ]
                    ]
                );

        return $general;
    }
}
