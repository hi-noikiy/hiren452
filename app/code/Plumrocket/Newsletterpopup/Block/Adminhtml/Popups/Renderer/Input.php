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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Block\Adminhtml\Popups\Renderer;

use Magento\Framework\DataObject;

class Input extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{
    /**
     * Renders grid column
     *
     * @param   DataObject $row
     * @return  string
     */
    public function render(DataObject $row)
    {
        if ('agreement' == $row->getName()) {
            return $this->renderTextarea($row);
        }

        return parent::render($row);
    }

    /**
     * Renders textarea
     *
     * @param   DataObject $row
     * @return  string
     */
    public function renderTextarea(DataObject $row)
    {
        $html = '<textarea ';
        $html .= 'name="' . $this->getColumn()->getId() . '" ';
        $html .= 'rows="3" ';
        $html .= 'class="input-text input-text-textarea ' . $this->getColumn()->getInlineCss() . '">';
        $html .= $this->escapeHtml($row->getData($this->getColumn()->getIndex()));
        $html .= '</textarea>';
        return $html;
    }
}
