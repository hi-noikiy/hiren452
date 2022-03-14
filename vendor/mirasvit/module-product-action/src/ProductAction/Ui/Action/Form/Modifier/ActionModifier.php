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

namespace Mirasvit\ProductAction\Ui\Action\Form\Modifier;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Mirasvit\ProductAction\Api\ActionInterface;
use Mirasvit\ProductAction\Registry;
use Mirasvit\ProductAction\Repository\ActionRepository;
use Mirasvit\ProductAction\Ui\Action\Form\DataProviderFactory;

class ActionModifier implements ModifierInterface
{
    private $actionRepository;

    private $request;

    private $registry;

    private $storeManager;

    private $dataProviderFactory;

    public function __construct(
        ActionRepository $actionRepository,
        RequestInterface $request,
        Registry $registry,
        StoreManagerInterface $storeManager,
        DataProviderFactory $dataProviderFactory
    ) {
        $this->actionRepository    = $actionRepository;
        $this->request             = $request;
        $this->registry            = $registry;
        $this->storeManager        = $storeManager;
        $this->dataProviderFactory = $dataProviderFactory;
    }

    public function modifyData(array $data): array
    {
        return $data;
    }

    public function modifyMeta(array $meta): array
    {
        $this->registry->setCurrentStore($this->storeManager->getStore());

        $action = $this->registry->getCurrentAction();

        if ($action->isAjaxMode()) { # in the ajax mode, modal already added
            $modalMeta = $this->addActionForm($action);

            foreach ($action->getMeta() as $child) {
                if (isset($child['arguments']['data']['config']['name'])) {
                    $name = $child['arguments']['data']['config']['name'];

                    unset($child['arguments']['data']['config']['name']);

                    $modalMeta['children'][$name] = $child;
                } else {
                    $modalMeta['children'][] = $child;
                }
            }

            $meta['form'] = $modalMeta;

            return $meta;
        }

        $modalMeta = $this->addActionModal($action);

        foreach ($action->getMeta() as $child) {
            if (isset($child['arguments']['data']['config']['name'])) {
                $name = $child['arguments']['data']['config']['name'];

                unset($child['arguments']['data']['config']['name']);

                $modalMeta['children']['form']['children'][$name] = $child;
            } else {
                $modalMeta['children']['form']['children'][] = $child;
            }
        }

        $meta['modal'] = $modalMeta;

        return $meta;
    }

    private function addActionForm(ActionInterface $action): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config'    => [
                        'componentType' => Component\Form::NAME,
                    ],
                    'js_config' => [
                        'component' => 'Mirasvit_ProductAction/js/action/form',
                    ],
                    'template'  => 'templates/form/collapsible',
                    'label'     => 'General Information',
                    'dataScope' => 'xyz',
                ],
            ],
            'children'  => [
                'code' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Component\Form\Field::NAME,
                                'formElement'   => Component\Form\Element\Input::NAME,
                                'dataType'      => Component\Form\Element\DataType\Text::NAME,
                                'default'       => $action->getCode(),
                                'visible'       => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function addActionModal(ActionInterface $action): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'isTemplate'    => false,
                        'componentType' => Component\Modal::NAME,
                        'options'       => [
                            'type'       => 'popup',
                            'modalClass' => 'mst-product-action__modal',
                            'title'      => __($action->getLabel()),
                        ],
                        'imports'       => [
                            'state' => '!index=' . $action->getCode() . '_form:responseStatus',
                        ],
                    ],
                ],
            ],
            'children'  => [
                'form'   => [
                    'arguments' => [
                        'data' => [
                            'config'    => [
                                'componentType' => Component\Form::NAME,
                            ],
                            'js_config' => [
                                'component' => 'Mirasvit_ProductAction/js/action/form',
                            ],
                            'template'  => 'templates/form/collapsible',
                            'label'     => 'General Information',
                            'dataScope' => 'xyz',
                        ],
                    ],
                    'children'  => [
                        'code' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Component\Form\Field::NAME,
                                        'formElement'   => Component\Form\Element\Input::NAME,
                                        'dataType'      => Component\Form\Element\DataType\Text::NAME,
                                        'default'       => $action->getCode(),
                                        'visible'       => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement'   => 'container',
                                'componentType' => 'container',
                                'component'     => 'Magento_Ui/js/form/components/button',
                                'buttonClasses' => 'mst-product-action__process-button primary',
                                'actions'       => [
                                    [
                                        'targetName'    => '${ $.parentName }.form',
                                        'actionName'    => 'submit',
                                        '__disableTmpl' => ['targetName' => false],
                                    ],
                                ],
                                'title'         => 'Process',
                                'provider'      => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
