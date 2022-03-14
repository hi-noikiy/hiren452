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
 * @package   mirasvit/module-dynamic-category
 * @version   1.0.17
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



declare(strict_types=1);

namespace Mirasvit\DynamicCategory\Ui\Category\Form\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset as FieldsetRenderer;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Rule\Block\Conditions as RuleConditions;
use Magento\Rule\Model\Condition\AbstractCondition;
use Mirasvit\DynamicCategory\Api\Data\DynamicCategoryInterface;
use Mirasvit\DynamicCategory\Model\DynamicCategory\Rule;
use Mirasvit\DynamicCategory\Registry;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;

class Conditions extends Form implements TabInterface
{
    protected $_nameInLayout = 'conditions_serialized';

    private   $conditions;

    private   $dynamicCategoryRepository;

    private   $fieldsetRenderer;

    private   $formFactory;

    private   $registry;

    private   $context;

    public function __construct(
        DynamicCategoryRepository $dynamicCategoryRepository,
        RuleConditions $conditions,
        FieldsetRenderer $fieldsetRenderer,
        FormFactory $formFactory,
        Registry $registry,
        Context $context
    ) {
        $this->dynamicCategoryRepository = $dynamicCategoryRepository;

        $this->fieldsetRenderer = $fieldsetRenderer;
        $this->conditions       = $conditions;
        $this->formFactory      = $formFactory;
        $this->registry         = $registry;
        $this->context          = $context;

        parent::__construct($context);
    }

    public function getTabLabel(): string
    {
        return __('Conditions')->render();
    }

    public function getTabTitle(): string
    {
        return __('Conditions')->render();
    }

    public function canShowTab(): bool
    {
        return true;
    }

    public function isHidden(): bool
    {
        return false;
    }

    protected function _prepareForm(): Conditions
    {
        $formName = DynamicCategoryInterface::RULE_FORM_NAME;
        $category = $this->registry->getCurrentCategory();

        $model = $this->dynamicCategoryRepository->getByCategoryId((int)$category->getId());
        if (!$model) {
            $model = $this->dynamicCategoryRepository->create();
        }

        /** @var Rule $rule */
        $rule = $model->getRule();

        $form = $this->formFactory->create();
        $form->setData('html_id_prefix', 'rule_');

        $fieldsetName = 'dynamic_category_conditions_fieldset';

        $renderer = $this->fieldsetRenderer
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setData('new_child_url', $this->getUrl('mst_dynamic_category/rule/newConditionHtml', [
                'form'      => 'rule_' . $fieldsetName,
                'form_name' => $formName,
            ]));

        $fieldset = $form->addFieldset($fieldsetName, [])->setRenderer($renderer);

        $rule->getConditions()
            ->setFormName($formName);

        $conditionsField = $fieldset->addField('conditions', 'text', [
            'name'           => 'conditions',
            'required'       => true,
            'data-form-part' => $formName,
        ]);

        $conditionsField->setRule($rule)
            ->setRenderer($this->conditions)
            ->setFormName($formName);

        $form->setValues($model->getData());

        $this->setConditionFormName($rule->getConditions(), $formName);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    private function setConditionFormName(AbstractCondition $conditions, string $formName): void
    {
        $conditions->setFormName($formName);

        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}
