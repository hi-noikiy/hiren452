<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Block\Adminhtml\Popups\Edit\Renderer;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class FieldArray
 *
 * @package Plumrocket\Newsletterpopup\Block\Adminhtml\Popups\Edit\Renderer
 */
class FieldArray extends AbstractFieldArray
{
    /**
     * @var string
     */
    const NETWORK_ID = 'sendy';

    /**
     * Prepare to render
     *
     * @var void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('list_id', ['label' => __('List ID'), 'class' => 'required-entry']);
        $this->addColumn('name', ['label' => __('List Name'), 'class' => 'required-entry']);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add List');
    }
}