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

namespace Plumrocket\Newsletterpopup\Block\Adminhtml\System\Config\Form\Integration\CustomFields;

/**
 * @method string getServiceId()
 * @method $this  setServiceId($serviceId)
 */
class FieldsList extends \Magento\Backend\Block\Template
{
    /**
     * @return false|string
     */
    public function getJsLayout()
    {
        $layout = json_decode(parent::getJsLayout(), true);
        $layout['components'][$this->getComponentName()] = $this->getJsComponentConfig();
        return json_encode($layout);
    }

    /**
     * @return string
     */
    public function getComponentName()
    {
        return 'pr-integration-' . $this->getServiceId() . '-custom-fields';
    }

    /**
     * @return array
     */
    private function getJsComponentConfig()
    {
        return [
            'component' => 'Plumrocket_Newsletterpopup/js/view/customFields',
            'url' => $this->getUrl('prnewsletterpopup/integration/customFields_' . $this->getServiceId()),
        ];
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml() // @codingStandardsIgnoreLine
    {
        $this->setTemplate('Plumrocket_Newsletterpopup::integration/custom_fields.phtml');
        return parent::_beforeToHtml();
    }
}
