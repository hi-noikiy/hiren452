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




namespace Mirasvit\ProductKit\Controller\Adminhtml\Kit;

use Magento\Rule\Model\Condition\AbstractCondition;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Controller\Adminhtml\AbstractKit;
use Mirasvit\ProductKit\Model\Rule\Rule;
use Mirasvit\ProductKit\Ui\Kit\Form\Modifier\SmartItemModifier;

class NewConditionHtml extends AbstractKit
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        $position  = (int)$this->getRequest()->getParam('position');
        $typeArr   = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $class     = $typeArr[0];
        $attribute = false;

        if (count($typeArr) == 2) {
            $attribute = $typeArr[1];
        }

        /** @var Rule $rule */
        $rule = $this->context->getObjectManager()->create(Rule::class);
        $rule->setElementName($this->getElementName($position));
        $model = $this->context->getObjectManager()
            ->create($class, ['elementName' => $this->getElementName($position)])
            ->setId($id)
            ->setType($class)
            ->setRule($rule)
            ->setPrefix('conditions')
            ->setFormName($this->getRequest()->getParam('form_name', Rule::FORM_NAME));

        $model->setAttribute($attribute);
        $model->setElementName($this->getElementName($position));

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }

        $this->getResponse()->setBody($html);
    }

    /**
     * @param string $position
     * @return string
     */
    public function getElementName($position)
    {
        return SmartItemModifier::SMART_ITEM . '[' . $position . '][' . KitItemInterface::CONDITIONS . ']';
    }
}
