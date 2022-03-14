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



namespace Mirasvit\ProductKit\Ui\Kit\Form\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset as FieldsetRenderer;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Repository\KitItemRepository;
use Mirasvit\ProductKit\Ui\Kit\Form\Modifier\SmartItemModifier;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Rule extends Form implements TabInterface
{
    private $conditionsRenderer;

    private $fieldsetRenderer;

    private $conditions;

    private $formFactory;

    private $kitItemRepository;

    private $registry;

    private $context;

    public function __construct(
        Conditions $conditions,
        ConditionsRenderer $conditionsRenderer,
        FieldsetRenderer $fieldsetRenderer,
        FormFactory $formFactory,
        Registry $registry,
        KitItemRepository $kitItemRepository,
        Context $context,
        array $data = []
    ) {
        $this->conditionsRenderer = $conditionsRenderer;
        $this->fieldsetRenderer   = $fieldsetRenderer;
        $this->conditions         = $conditions;
        $this->formFactory        = $formFactory;
        $this->registry           = $registry;
        $this->kitItemRepository  = $kitItemRepository;
        $this->context            = $context;

        parent::__construct($context, $data);
    }

    public function setLayout(\Magento\Framework\View\LayoutInterface $layout)
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * {@inheritDoc}
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * {@inheritDoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $formName      = \Mirasvit\ProductKit\Model\Rule\Rule::FORM_NAME;
        $conditionName = $this->getConditionsName();

        /** @var KitInterface $scoreRule */
        $scoreRule = $this->registry->registry(KitInterface::class);
        $rule      = $this->kitItemRepository->create()->getRule();
        if ($scoreRule->getId() && $scoreRule->isSmart()) {
            $items = $this->kitItemRepository->getItems($scoreRule);
            if ($items && isset($items[$this->getPosition()])) {
                $rule = $items[$this->getPosition()]->getRule();
            }
        }

        $form = $this->formFactory->create();

        $form->setHtmlIdPrefix('rule_');

        $renderer = $this->fieldsetRenderer
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setData('new_child_url', $this->getUrl('*/kit/newConditionHtml', [
                'form'      => $this->getJsFormObjectName(),
                'form_name' => $formName,
                'position'  => $this->getPosition(),
            ]));

        $fieldset = $form->addFieldset(
            $conditionName . '_fieldset',
            [
                'legend' => __('Apply the rule only for the following products: '),
                'class'  => 'fieldset',
            ]
        )->setRenderer($renderer);


        $rule->getConditions()
            ->setElementName($this->getElementName())
            ->setFormName($formName);

        $conditionsField = $fieldset->addField($conditionName, 'text', [
            'name'           => $conditionName,
            'required'       => true,
            'data-form-part' => $formName,
        ]);

        $conditionsField->setRule($rule)
            ->setRenderer($this->conditionsRenderer)
            ->setFormName($formName);

        $this->setConditionFormName($rule->getConditions(), $formName);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param object $conditions
     * @param string $formName
     *
     * @return void
     */
    private function setConditionFormName($conditions, $formName)
    {
        $conditions->setFormName($formName);
        $conditions->setElementName($this->getElementName());
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
                $condition->setElementName($this->getElementName());
            }
        }
        $conditions->setJsFormObject($this->getJsFormObjectName());
    }

    /**
     * @return string
     */
    private function getConditionsName()
    {
        return 'conditions_' . $this->getPosition();
    }

    /**
     * @return string
     */
    private function getJsFormObjectName()
    {
        return 'rule_' . $this->getConditionsName() . '_fieldset';
    }

    private function getElementName()
    {
        return SmartItemModifier::SMART_ITEM . '[' . $this->getPosition() . '][' . KitItemInterface::CONDITIONS . ']';
    }

    /**
     * @return int
     */
    private function getPosition()
    {
        return $this->getData(KitItemInterface::POSITION);
    }
}
