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

namespace Mirasvit\ProductAction\Ui\Action\Block;

use Magento\Backend\Block\Template;
use Magento\Framework\View\Element\UiComponent\ContextFactory as UiComponentContextFactory;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\ProductAction\Api\ActionInterface;
use Mirasvit\ProductAction\Registry;
use Mirasvit\ProductAction\Repository\ActionRepository;

class ActionBlock extends Template
{
    private $actionRepository;

    private $registry;

    private $uiComponentFactory;

    private $uiComponentContextFactory;

    public function __construct(
        Registry $registry,
        ActionRepository $actionRepository,
        UiComponentFactory $uiComponentFactory,
        UiComponentContextFactory $uiComponentContextFactory,
        Template\Context $context,
        array $data = []
    ) {
        $this->registry                  = $registry;
        $this->actionRepository          = $actionRepository;
        $this->uiComponentFactory        = $uiComponentFactory;
        $this->uiComponentContextFactory = $uiComponentContextFactory;
        parent::__construct($context, $data);
    }

    public function toHtml()
    {
        $html = [];

        foreach ($this->actionRepository->getList() as $action) {
            $this->registry->setCurrentAction($action);

            if ($this->hasData(ActionInterface::CODE)) {
                if ($this->getData(ActionInterface::CODE) == $action->getCode()) {
                    $html[] = $this->getFormHtml();
                }
            } else {
                $html[] = $action->isAjaxMode() ? $this->getAjaxFormHtml() : $this->getFormHtml();
            }
        }

        return implode(PHP_EOL, $html);
    }

    protected function prepareComponent(UiComponentInterface $component)
    {
        $childComponents = $component->getChildComponents();
        if (!empty($childComponents)) {
            foreach ($childComponents as $child) {
                $this->prepareComponent($child);
            }
        }
        $component->prepare();
    }

    private function getAjaxFormHtml(): string
    {
        $action = $this->registry->getCurrentAction();

        $context = $this->uiComponentContextFactory->create([
            'namespace' => 'mst_product_action_action_ajaxform',
        ]);

        $component = $this->uiComponentFactory->create('mst_product_action_action_ajaxform', null, [
            'context'    => $context,
            'pageLayout' => $this->_layout,
        ]);

        $this->prepareComponent($component);

        if (CompatibilityService::is24()) {
            $componentHtml = $component->toHtml();
        } else {
            $componentHtml = (string)$component->render();
        }

        $componentHtml = str_replace('mst_product_action_action_ajaxform', 'mst_product_action_action_ajaxform_' . $action->getCode(), $componentHtml);
        $componentHtml = str_replace('admin__form-loading-mask', '', $componentHtml);
        $componentHtml = str_replace('class="spinner"', '', $componentHtml);

        return $componentHtml;
    }

    private function getFormHtml(): string
    {
        $action = $this->registry->getCurrentAction();

        $context = $this->uiComponentContextFactory->create([
            'namespace' => 'mst_product_action_action_form',
        ]);

        $component = $this->uiComponentFactory->create('mst_product_action_action_form', null, [
            'context'    => $context,
            'pageLayout' => $this->_layout,
        ]);

        $this->prepareComponent($component);

        if (CompatibilityService::is24()) {
            $componentHtml = $component->toHtml();
        } else {
            $componentHtml = (string)$component->render();
        }

        $componentHtml = str_replace('mst_product_action_action_form', 'mst_product_action_action_form_' . $action->getCode(), $componentHtml);
        $componentHtml = str_replace('admin__form-loading-mask', '', $componentHtml);
        $componentHtml = str_replace('class="spinner"', '', $componentHtml);

        return $componentHtml;
    }
}
