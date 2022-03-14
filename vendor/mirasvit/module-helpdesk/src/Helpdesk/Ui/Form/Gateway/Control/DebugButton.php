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
 * @package   mirasvit/module-helpdesk
 * @version   1.1.149
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Helpdesk\Ui\Form\Gateway\Control;

use Mirasvit\Helpdesk\Ui\Form\Button;

class DebugButton extends Button
{
    /**
     * {@inheritdoc}
     */
    public function getButtonUrl()
    {
        return sprintf("location.href = '%s';", $this->getUrl('*/*/debug', [self::ID_NAME => $this->getId()]));
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Debug');
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return 'debug';
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }
    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->context->getRequest()->getParam(self::ID_NAME);
    }
}
