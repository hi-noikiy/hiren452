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

namespace Mirasvit\ProductAction\Ui\Action\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\ProductAction\Registry;

class AjaxDataProvider extends AbstractDataProvider
{
    private $actionModifier;

    private $registry;

    private $request;

    private $urlBuilder;

    /**
     * @param Registry                $registry
     * @param UrlInterface            $urlBuilder
     * @param Modifier\ActionModifier $actionModifier
     * @param RequestInterface        $request
     * @param string                  $name
     * @param string                  $primaryFieldName
     * @param string                  $requestFieldName
     * @param array                   $meta
     * @param array                   $data
     */
    public function __construct(
        Registry $registry,
        UrlInterface $urlBuilder,
        Modifier\ActionModifier $actionModifier,
        RequestInterface $request,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->actionModifier = $actionModifier;
        $this->registry       = $registry;
        $this->request        = $request;
        $this->urlBuilder     = $urlBuilder;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getMeta(): array
    {
        $action = $this->registry->getCurrentAction();

        if (!$action) {
            return [];
        }

        return [
            'mst_product_action_form_modal' => [
                'arguments'  => [
                    'data' => [
                        'config' => [
                            'options'       => [
                                'type'       => 'popup',
                                'title'      => $action->getLabel(),
                                'modalClass' => 'mst-product-action__modal mst-product-action__' . $action->getCode(),
                            ],
                            'component'     => 'Magento_Ui/js/modal/modal-component',
                            'componentType' => Component\Modal::NAME,
                        ],
                    ],
                ],
                'attributes' => [
                    'class'     => 'Magento\Ui\Component\Container',
                    'component' => 'Magento_Ui/js/modal/modal-component',
                    'name'      => 'mst_product_action_form_modal',
                ],
                'children'   => [
                    'mst_product_action_form_loader' => [
                        'arguments'  => [
                            'data' => [
                                'config' => [
                                    'autoRender'     => false,
                                    'realTimeLink'   => false,
                                    'render_url'     => $this->urlBuilder->getUrl('mst_product_action/action/ajaxload', ['code' => $action->getCode()]),
                                    'update_url'     => $this->urlBuilder->getUrl('mui/index/render'),
                                    'component'      => 'Magento_Ui/js/form/components/insert-form',
                                    'componentType'  => Component\Form::NAME,
                                    'ns'             => 'mst_product_action_form',
                                    'formSubmitType' => 'ajax',
                                ],
                            ],
                        ],
                        'attributes' => [
                            'class'     => 'Magento\Ui\Component\Container',
                            'component' => 'Magento_Ui/js/form/components/insert-form',
                            'name'      => 'mst_product_action_attribute_ajax_form_loader',
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
                                            'targetName'    => 'mst_product_action_action_form_mass_attributes_update.mst_product_action_action_form_mass_attributes_update.form',
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
            ],
        ];
    }

    public function setLimit($offset, $size)
    {
    }

    public function addField($field, $alias = null)
    {
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {

    }

    public function getData(): array
    {
        return [];
    }
}
