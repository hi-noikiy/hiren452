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

namespace Mirasvit\DynamicCategory\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Rule\Model\Condition\AbstractCondition;
use Mirasvit\DynamicCategory\Model\DynamicCategory\Rule;

class NewConditionHtml extends Action// implements HttpPostActionInterface
{
    public function execute(): void
    {
        $id = (string)$this->getRequest()->getParam('id');

        $typeArr   = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $class     = $typeArr[0];
        $attribute = false;

        if (count($typeArr) == 2) {
            $attribute = $typeArr[1];
        }

        $objectManager = ObjectManager::getInstance();

        $model = $objectManager->create($class)
            ->setId($id)
            ->setType($class)
            ->setRule($objectManager->create(Rule::class))
            ->setPrefix('conditions')
            ->setFormName($this->getRequest()->getParam('form_name', Rule::FORM_NAME));

        $model->setAttribute($attribute);

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }

        $this->getResponse()->setBody($html);
    }
}
