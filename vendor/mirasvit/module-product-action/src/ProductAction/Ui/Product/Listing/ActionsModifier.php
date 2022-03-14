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

namespace Mirasvit\ProductAction\Ui\Product\Listing;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Mirasvit\ProductAction\Repository\ActionRepository;
use Mirasvit\ProductAction\Ui\Action\Form\DataProviderFactory;
use Mirasvit\ProductAction\Ui\Element\GroupTrait;
use Mirasvit\ProductAction\Ui\Element\SelectTrait;

class ActionsModifier implements ModifierInterface
{
    use GroupTrait;
    use SelectTrait;

    private $actionRepository;

    private $request;

    private $urlBuilder;

    private $uiComponentFactory;

    private $dataProviderFactory;

    public function __construct(
        ActionRepository $actionRepository,
        RequestInterface $request,
        UrlInterface $urlBuilder,
        UiComponentFactory $uiComponentFactory,
        DataProviderFactory $dataProviderFactory
    ) {
        $this->actionRepository    = $actionRepository;
        $this->request             = $request;
        $this->urlBuilder          = $urlBuilder;
        $this->uiComponentFactory  = $uiComponentFactory;
        $this->dataProviderFactory = $dataProviderFactory;
    }

    public function modifyMeta(array $meta): array
    {
        if ($this->request->getParam('namespace')) {
            return $meta;
        }

        foreach ($this->actionRepository->getList() as $action) {
            $callback = $action->isAjaxMode()
                ? [
                    [
                        'targetName' => 'mst_product_action_action_ajaxform_' . $action->getCode() . '.mst_product_action_action_ajaxform_' . $action->getCode() . '.mst_product_action_form_modal',
                        'actionName' => 'toggleModal',
                    ],
                ]
                : [
                    [
                        'targetName' => 'mst_product_action_action_form_' . $action->getCode() . '.mst_product_action_action_form_' . $action->getCode() . '.modal',
                        'actionName' => 'toggleModal',
                    ],
                ];

            $meta['listing_top']['children']['listing_massaction']['children'][$action->getCode()] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'action',
                            'label'         => $action->getLabel(),
                            'type'          => $action->getCode(),
                            'callback'      => $callback,
                        ],
                    ],
                ],
            ];
        }

        $meta['product_columns']['arguments']['data']['config']['sortOrder'] = 10;

        $meta['mst_product_action_quick_actions_block'] = $this->elementGroup([], [
            'sortOrder' => 9,
            'component' => 'Mirasvit_ProductAction/js/product/quick-actions',
            'template'  => 'Mirasvit_ProductAction/product/quick-actions',
        ]);

        return $meta;
    }

    public function modifyData(array $data): array
    {
        return $data;
    }

    protected function prepareComponent(UiComponentInterface $component): array
    {
        $data = [];
        foreach ($component->getChildComponents() as $child) {
            $data['children'][$child->getName()] = $this->prepareComponent($child);
        }

        $component->prepare();
        $data['arguments']['data'] = $component->getData();
        unset($data['arguments']['data']['options']);

        return $data;
    }
}
