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
 * @package   mirasvit/module-feed
 * @version   1.1.32
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab;

use Magento\Backend\Block\Widget\Container;

class History extends Container
{
    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        return $this->getLayout()->createBlock('\Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab\History\Grid')->toHtml();
    }
}
