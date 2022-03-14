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
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
declare(strict_types=1);

namespace Plumrocket\AmpEmail\Block\Adminhtml\Email\Template\Renderer;

/**
 * @method setElementParams(array $array)
 */
class SentTestEmail extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * @var string
     */
    protected $_template = 'Plumrocket_AmpEmail::widget/form/renderer/send_test_email.phtml'; //@codingStandardsIgnoreLine

    /**
     * @return string
     */
    public function getSendButtonHtml() : string
    {
        $prampLoadTemplateButton = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class,
            'pramp_email_send_test_email_button',
            [
                'data' => [
                    'type' => 'button',
                    'id' => 'pramp_email_send_test_email_button',
                    'label' => __('Send'),
                    'onclick' => 'window.prampSendTestEmail();return false;',
                ]
            ]
        );

        return $prampLoadTemplateButton->toHtml();
    }

    /**
     * @param $paramName
     * @return mixed
     */
    public function getElementParams(string $paramName) : string
    {
        return (string) $this->getData('element_params/' . $paramName);
    }

    /**
     * @return string
     */
    public function getTestSendUrl() : string
    {
        return $this->getUrl('prampemail/template/send');
    }
}
