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
 * @package     Plumrocket SMTP
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Smtp\Block\Adminhtml\System\Config\Form;

class Button extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(// @codingStandardsIgnoreLine we need to extend parent method
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        return $this->getButtonHtml($element, 'test_email');
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @param                                                      $serviceId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getButtonHtml(
        \Magento\Framework\Data\Form\Element\AbstractElement $element,
        $serviceId
    ) {
        $disabled = $this->isDisabled();

        /** @var \Magento\Backend\Block\Widget\Button $button */
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        );

        $button->setData([
            'id' => (string)$element->getHtmlId(),
            'label' => __('Send'),
            'class'     => 'prsmtp_send_test_email',
            'onclick' => sprintf('javascript:window.sendEmail("%s");', $this->getUrl('prsmtp/send/email')),
            'disabled' => $disabled,
        ]);

        return $button->toHtml();
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * Determine if button is disabled
     *
     * @return boolean
     */
    protected function isDisabled()
    {
        return false;
    }
}
