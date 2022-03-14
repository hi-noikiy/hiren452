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

class QrCode extends \Magezon\ProductPagePdf\Data\Element
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

	    	$general->addChildren(
	            'qrcode_data',
	            'textarea',
	            [
					'key'             => 'qrcode_data',
					'sortOrder'       => 10,
					'defaultValue'    => '{{var product.url}}',
					'templateOptions' => [
						'label' => __('QR Code Data'),
						'note'  => __('Specifies the size of the barcode.')
	                ]
	            ]
	        );

	    	$container1 = $general->addContainerGroup(
                'container1',
                [
                    'sortOrder' => 20   
                ]
			);
			
				$container1->addChildren(
					'size',
					'number',
					[
						'key'             => 'size',
						'sortOrder'       => 10,
						'defaultValue'    => 2,
						'templateOptions' => [
							'label' => __('Size')
						]
					]
				);
				$container1->addChildren(
					'enable_border',
					'toggle',
					[
						'key'             => 'enable_border',
						'sortOrder'       => 20,
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Enable Border')
						]
					]
				);

    	return $general;
    }
}
