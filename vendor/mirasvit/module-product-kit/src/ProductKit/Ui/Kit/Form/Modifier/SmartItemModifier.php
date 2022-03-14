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
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Ui\Kit\Form\Modifier;

use Magento\Framework\View\Layout;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\HtmlContent;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Repository\KitItemRepository;
use Mirasvit\ProductKit\Ui\Kit\Source\DiscountTypeSource;

class SmartItemModifier
{
    const FORM_SMART_BLOCK_INDEX = 'product_';

    const SMART_ITEM = 'smart_item';

    private $layout;

    private $itemRepository;

    private $discountTypeSource;

    public function __construct(
        Layout $layout,
        KitItemRepository $itemRepository,
        DiscountTypeSource $discountTypeSource
    ) {
        $this->layout             = $layout;
        $this->itemRepository     = $itemRepository;
        $this->discountTypeSource = $discountTypeSource;
    }

    public function modifyMeta(array $meta)
    {
        $children = [];
        for ($i = 1; $i <= KitInterface::SMART_BLOCKS_DEFAULT; $i++) {
            $children[] = $this->createSmartBlock($i);
        }

        $meta['smart_item_container'] = [
            'arguments'  => [
                'data' => [
                    'config' => [
                        'sortOrder'         => 35,
                        'template'          => 'Mirasvit_ProductKit/kit/form/smart-items',
                        'componentType'     => Container::NAME,
                        'additionalClasses' => 'admin__fieldset-section',
                    ],
                ],
            ],
            'attributes' => [
                'class' => Container::class,
            ],
            'children'   => $children,
        ];

        return $meta;
    }

    public function modifyData(KitInterface $kit, array $data)
    {
        $items = $this->itemRepository->getItems($kit);

        $numberOfItems = 0;
        foreach ($items as $item) {
            if ($item->getQty() > 0) {
                $numberOfItems++;
            }

            $itemData = $item->getData();

            unset($itemData[KitItemInterface::CONDITIONS]);
            $itemData[KitItemInterface::DISCOUNT_AMOUNT] = round($itemData[KitItemInterface::DISCOUNT_AMOUNT], 2);

            foreach ($itemData as $k => $v) {
                $data[self::SMART_ITEM][$item->getPosition()] = $itemData;
            }

            $data[self::SMART_ITEM][$item->getPosition()][KitItemInterface::IS_REMOVED] = $item->getId() > 0 ? 1 : 0;
        }

        $data['smart_items_number'] = $numberOfItems;

        return $data;
    }

    /**
     * @param int    $position
     * @param string $field
     *
     * @return string
     */
    private function getSmartItemName($position, $field)
    {
        return self::SMART_ITEM . '.' . $position . '.' . $field;
    }

    /**
     * Get letter by its number in alphabet
     *
     * @param int $position
     *
     * @return string
     */
    private function getLetter($position)
    {
        return chr(64 + $position);
    }

    /**
     * @param int $position
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function createSmartBlock($position)
    {
        return [
            'arguments'  => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'component'     => 'Mirasvit_ProductKit/js/kit/form/component/smartItemFieldset',
                        'label'         => __('Product %1', $this->getLetter($position))->render(),
                        'collapsible'   => 1,
                        'sortOrder'     => $position,
                        'opened'        => 1,
                        'position'      => $position,
                    ],
                ],
            ],
            'attributes' => [
                'class' => Fieldset::class,
            ],
            'children'   => [
                KitItemInterface::IS_REMOVED      => [
                    'arguments'  => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'visible'       => false,
                                'dataType'      => 'text',
                                'formElement'   => 'input',
                                'source'        => 'template',
                                'dataScope'     => $this->getSmartItemName($position, KitItemInterface::IS_REMOVED),
                                'value'         => $position,
                            ],
                        ],
                    ],
                    'attributes' => [
                        'class' => Field::class,
                    ],
                ],
                KitItemInterface::POSITION        => [
                    'arguments'  => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'visible'       => false,
                                'dataType'      => 'text',
                                'formElement'   => 'input',
                                'source'        => 'template',
                                'dataScope'     => $this->getSmartItemName($position, KitItemInterface::POSITION),
                                'value'         => $position,
                            ],
                        ],
                    ],
                    'attributes' => [
                        'class' => Field::class,
                    ],
                ],
                KitItemInterface::IS_PRIMARY      => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'dataType'      => 'boolean',
                                'label'         => __('Is Primary')->render(),
                                'formElement'   => 'checkbox',
                                'sortOrder'     => 25,
                                'prefer'        => 'toggle',
                                'default'       => '1',
                                'dataScope'     => $this->getSmartItemName($position, KitItemInterface::IS_PRIMARY),
                                'valueMap'      => [
                                    'true'  => '1',
                                    'false' => '0',
                                ],
                            ],
                        ],
                    ],

                    'attributes' => [
                        'class' => Field::class,
                    ],
                ],
                KitItemInterface::IS_OPTIONAL     => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'dataType'      => 'boolean',
                                'label'         => __('Is Optional')->render(),
                                'formElement'   => 'checkbox',
                                'sortOrder'     => 30,
                                //'disabled'      => $position <= 2,
                                'prefer'        => 'toggle',
                                'default'       => '0',
                                'dataScope'     => $this->getSmartItemName($position, KitItemInterface::IS_OPTIONAL),
                                'valueMap'      => [
                                    'true'  => '1',
                                    'false' => '0',
                                ],
                            ],
                        ],
                    ],

                    'attributes' => [
                        'class' => Field::class,
                    ],
                ],
                KitItemInterface::DISCOUNT_AMOUNT => [
                    'arguments'  => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'dataType'      => 'text',
                                'label'         => __('Discount')->render(),
                                'dataScope'     => $this->getSmartItemName($position, KitItemInterface::DISCOUNT_AMOUNT),
                                'formElement'   => 'input',
                                'sortOrder'     => 20,
                                'options'       => $this->discountTypeSource->toOptionArray(),
                                'component'     => 'Mirasvit_ProductKit/js/kit/form/discount',
                                'validation'    => [
                                    'required-entry' => 1,
                                ],
                            ],
                        ],
                    ],
                    'attributes' => [
                        'class' => Field::class,
                    ],
                ],
                KitItemInterface::QTY             => [
                    'arguments'  => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'dataType'      => 'text',
                                'label'         => __('Qty')->render(),
                                'formElement'   => 'input',
                                'sortOrder'     => 40,
                                'dataScope'     => $this->getSmartItemName($position, KitItemInterface::QTY),
                                'validation'    => [
                                    'required-entry' => 1,
                                ],
                            ],
                        ],
                    ],
                    'attributes' => [
                        'class' => Field::class,
                    ],
                ],
                'html_content'                    => [
                    'arguments'  => [
                        'data'  => [
                            'config' => [
                                'componentType' => 'htmlContent',
                                'component'     => 'Magento_Ui/js/form/components/html',
                                'showSpinner'   => 1,
                            ],
                        ],
                        'block' => $this->layout->createBlock(
                            \Mirasvit\ProductKit\Ui\Kit\Form\Block\Rule::class,
                            'product_rule_' . $position,
                            [
                                'data' => [
                                    KitItemInterface::POSITION => $position,
                                ],
                            ]
                        ),
                    ],
                    'attributes' => [
                        'class'     => HtmlContent::class,
                        'component' => 'Magento_Ui/js/form/components/html',
                        'name'      => 'html_content',
                    ],
                ],
            ],
        ];
    }
}
