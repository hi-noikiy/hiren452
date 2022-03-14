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

class Gallery extends \Magezon\ProductPagePdf\Data\Element
{
    /**
     * Prepare modal components
     */
    public function prepareForm()
    {
    	parent::prepareForm();
    	$this->prepareGalleryTab();
    	return $this;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGalleryTab()
    {
    	$gallery = $this->addTab(
            'gallery',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('General')
                ]
            ]
        );

            $gallery->addChildren(
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

	        $container1 = $gallery->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 10
	            ]
	        );

	            $container1->addChildren(
	                'gallery_navposition',
	                'select',
	                [
						'sortOrder'       => 10,
						'key'             => 'gallery_navposition',
						'defaultValue'    => 'bottom',
						'templateOptions' => [
							'label'   => __('Product Gallery Type'),
							'options' => $this->getLayoutGalerry(),
							'tooltip' => __('Position of thumbnails. Default: Bottom')
	                    ]
	                ]
	            );

	        $container2 = $gallery->addContainerGroup(
	            'container2',
	            [
					'sortOrder' => 20
	            ]
	        );

	            $container2->addChildren(
	                'enable_img_border',
	                'toggle',
	                [
						'sortOrder'       => 10,
						'key'             => 'enable_img_border',
						'defaultValue'    => true,
						'templateOptions' => [
							'label'        => __('Enable Image Border'),
							'tooltip'      => __('Turn on/off border for images. Default: true'),
							'tooltipClass' => 'tooltip-top tooltip-top-right'
	                    ]
	                ]
                );

                $container2->addChildren(
	                'border_img_width',
	                'number',
	                [
						'sortOrder'       => 20,
						'key'             => 'border_img_width',
						'templateOptions' => [
                            'label'        => __('Border Image Width'),
                        ],
                        'hideExpression' => '!model.enable_img_border'
	                ]
                );
                
                $container2->addChildren(
	                'border_img_color',
	                'color',
	                [
						'sortOrder'       => 30,
                        'key'             => 'border_img_color',
                        'defaultValue'    => '#dddddd',
						'templateOptions' => [
                            'label'        => __('Border Image Color'),
                        ],
                        'hideExpression' => '!model.enable_img_border'
	                ]
                );
                
            $container3 = $gallery->addContainerGroup(
                'container3',
                [
                    'sortOrder' => 30
                ]
            );
                $container3->addChildren(
                    'count_image',
                    'number',
                    [
                        'sortOrder'       => 5,
                        'key'             => 'count_image',
                        'defaultValue'    => 10,
                        'templateOptions' => [
                            'label'        => __('Count Image'),
                            'tooltip'      => __('Maximum display image number. Default: 10'),
                            'tooltipClass' => 'tooltip-top tooltip-top-right'
                        ]
                    ]
                );

                $container3->addChildren(
                    'enable_child_img',
                    'toggle',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'enable_child_img',
                        'defaultValue'    => true,
                        'templateOptions' => [
                            'label'        => __('Enable Child Image'),
                            'tooltip'      => __('Turn on/off Children images. Default: true'),
                            'tooltipClass' => 'tooltip-top tooltip-top-left'
                        ]
                    ]
                );

                $container3->addChildren(
                    'filter_same_img',
                    'toggle',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'filter_same_img',
                        'defaultValue'    => true,
                        'templateOptions' => [
                            'label'       => __('Filter Same Image'),
                            'tooltip'      => __('Removes the same images. Default: true'),
                            'tooltipClass' => 'tooltip-top tooltip-top-left'
                        ],
                        'hideExpression' => '!model.enable_child_img'
                    ]
                );
            
            $gallery->addChildren(
                'enable_title',
                'toggle',
                [
                    'sortOrder'       => 31,
                    'key'             => 'enable_title',
                    'defaultValue'    => false,
                    'templateOptions' => [
                        'label' => __('Enable Title')
                    ]
                ]
            );

            $container4 = $gallery->addContainerGroup(
                'container4',
                [
                    'sortOrder' => 50
                ]
            );
                $container4->addChildren(
                    'head_title',
                    'text',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'head_title',
                        'defaultValue'    => "Gallery",
                        'templateOptions' => [
                            'label' => __('Title Name')
                        ],
                        'hideExpression' => '!model.enable_title'
                    ]
                );
                $container4->addChildren(
                    'title_font_size',
                    'number',
                    [
                        'sortOrder'       => 30,
                        'key'             => 'title_font_size',
                        'defaultValue'    => 28,
                        'templateOptions' => [
                            'label' => __('Title font size')
                        ],
                        'hideExpression' => '!model.enable_title'
                    ]
                );
                $container4->addChildren(
                    'title_color',
                    'color',
                    [
                        'sortOrder'       => 40,
                        'key'             => 'title_color',
                        'defaultValue'    => '#444444',
                        'templateOptions' => [
                            'label' => __('Title Color')
                        ],
                        'hideExpression' => '!model.enable_title'
                    ]
                );
            
            $container5 = $gallery->addContainerGroup(
                'container5',
                [
                    'sortOrder' => 50
                ]
            );
                $container5->addChildren(
                    'enable_border_bottom',
                    'toggle',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'enable_border_bottom',
                        'defaultValue'    => false,
                        'templateOptions' => [
                            'label' => __('Enable Border Title'),
                            'tooltip'      => __('Add border for title in bottom. Default: false'),
                            'tooltipClass' => 'tooltip-top tooltip-top-right'
                        ],
                        'hideExpression' => '!model.enable_title'
                    ]
                );
                $container5->addChildren(
                    'border_title_width',
                    'text',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'border_title_width',
                        'defaultValue'    => 3,
                        'templateOptions' => [
                            'label' => __('Border Title Width')
                        ],
                        'hideExpression' => '!model.enable_title || !model.enable_border_bottom'
                    ]
                );
                $container5->addChildren(
                    'border_title_color',
                    'color',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'border_title_color',
                        'defaultValue'    => '#dddddd',
                        'templateOptions' => [
                            'label' => __('Border Title Color'),
                            'tooltip'      => __('Border Color. Default: #dddddd'),
                            'tooltipClass' => 'tooltip-top tooltip-top-left'
                        ],
                        'hideExpression' => '!model.enable_title || !model.enable_border_bottom'
                    ]
                );

        return $gallery;
    }

    /**
     * @return array
     */
    public function getLayoutGalerry()
    {
        return [
            [
                'label' => __('Simple'),
                'value' => 'simple'
            ],
            [
                'label' => __('Right'),
                'value' => 'right'
            ],
            [
                'label' => __('Bottom'),
                'value' => 'bottom'
            ],
            [
                'label' => __('Left'),
                'value' => 'left'
            ],
            [
                'label' => __('One Row'),
                'value' => 'one_row'
            ],
            [
                'label' => __('Two Row'),
                'value' => 'two_row'
            ],
        ];
    }
}
