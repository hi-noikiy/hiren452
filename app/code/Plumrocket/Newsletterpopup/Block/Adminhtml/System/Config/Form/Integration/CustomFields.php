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

namespace Plumrocket\Newsletterpopup\Block\Adminhtml\System\Config\Form\Integration;

use Plumrocket\Newsletterpopup\Block\Adminhtml\System\Config\Form\Integration\CustomFields\FieldsList;

class CustomFields extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(// @codingStandardsIgnoreLine we need to extend parent method
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        $serviceId = '';
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $container */
        $container = $element->getData('container');

        if ($container
            && ($group = $container->getData('group'))
            && (is_array($group) && ! empty($group['id']))
        ) {
            $serviceId = (string)$group['id'];
        }

        /** @var FieldsList $fieldsList */
        $fieldsList = $this->getLayout()->createBlock(
            FieldsList::class
        );
        $fieldsList->setServiceId($serviceId);

        return $this->getButtonHtml($element, $serviceId). $fieldsList->toHtml();
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @param $serviceId
     * @return string
     */
    private function getButtonHtml(
        \Magento\Framework\Data\Form\Element\AbstractElement $element,
        $serviceId
    ) {
        $disabled = $this->isDisabled();

        $url = $this->getUrl('prnewsletterpopup/integration/customFields_' . $serviceId);

        $onClick = sprintf('javascript:window.prnewsletterpopup.loadCustomFields.%s("%s");', $serviceId, $url);
        /** @var \Magento\Backend\Block\Widget\Button $button */
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        );
        $button->setData(
            [
                'id' => (string)$element->getHtmlId(),
                'label' => __('Load Fields from Constant Contact'),
                'class'     => 'pr-custom-fields-load',
                'onclick' => $onClick,
                'disabled' => $disabled,
            ]
        );

        return $button->toHtml();
    }

    /**
     * Determine if button is disabled
     *
     * @return boolean
     */
    protected function isDisabled() // @codingStandardsIgnoreLine
    {
        return false;
    }
}
