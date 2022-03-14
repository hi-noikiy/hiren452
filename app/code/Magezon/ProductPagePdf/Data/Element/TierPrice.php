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

class TierPrice extends \Magezon\ProductPagePdf\Data\Element
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
                    'sortOrder' => 30
                ]
            );
                $container2->addChildren(
                    'tierprice_font_size',
                    'number',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'tierprice_font_size',
                        'defaultValue'    => 28,
                        'templateOptions' => [
                            'label' => __('Price Tier Font Size')
                        ]
                    ]
                );
                $container2->addChildren(
                    'tierprice_color',
                    'color',
                    [
                        'sortOrder'       => 40,
                        'key'             => 'tierprice_color',
                        'defaultValue'    => '#333333',
                        'templateOptions' => [
                            'label' => __('Price Tier Color')
                        ]
                    ]
                );

        return $general;
    }
}
