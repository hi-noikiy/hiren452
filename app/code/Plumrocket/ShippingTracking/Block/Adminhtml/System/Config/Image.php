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
 * @package     Plumrocket_ShippingTracking
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ShippingTracking\Block\Adminhtml\System\Config;

class Image extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $addonHtml = '<img src="%s" id="' . $element->getHtmlId() . '" height="44" width="44" 
            class="small-image-preview v-middle" />';

        $id = explode('_', $element->getHtmlId());
        $imgUrl = $this->getViewFileUrl('Plumrocket_ShippingTracking::images/icons/' . end($id) . '.png');
        
        return sprintf($addonHtml, $imgUrl);
    }
}