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

class Load extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{

    /**
     * @var string
     */
    protected $_template = 'Plumrocket_AmpEmail::widget/form/renderer/template_loader.phtml'; //@codingStandardsIgnoreLine

    /**
     * @var \Plumrocket\AmpEmail\Model\Template\Config
     */
    private $templateConfig;

    /**
     * Load constructor.
     *
     * @param \Magento\Backend\Block\Template\Context    $context
     * @param \Plumrocket\AmpEmail\Model\Template\Config $templateConfig
     * @param array                                      $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Plumrocket\AmpEmail\Model\Template\Config $templateConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->templateConfig = $templateConfig;
    }

    /**
     * @return \Magento\Backend\Block\Template
     */
    protected function _beforeToHtml() //@codingStandardsIgnoreLine
    {
        $groupedOptions = [];
        foreach ($this->getDefaultTemplatesAsOptionsArray() as $option) {
            $groupedOptions[$option['group']][] = $option;
        }
        ksort($groupedOptions);
        $this->setData('template_options', $groupedOptions);

        return parent::_beforeToHtml();
    }

    /**
     * Get default templates as options array
     *
     * @return array
     */
    private function getDefaultTemplatesAsOptionsArray() : array
    {
        $options = array_merge(
            [['value' => '', 'label' => '', 'group' => '']],
            $this->templateConfig->getAvailableTemplates()
        );
        uasort(
            $options,
            static function (array $firstElement, array $secondElement) {
                return strcmp((string) $firstElement['label'], (string) $secondElement['label']);
            }
        );
        return $options;
    }

    /**
     * @return string
     */
    public function getLoadButtonHtml() : string
    {
        $prampLoadTemplateButton = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class,
            'pramp_email_load_template_button',
            [
                'data' => [
                    'type' => 'button',
                    'id' => 'pramp_email_load_template_button',
                    'label' => __('Load Template'),
                    'onclick' => 'window.prampLoadTemplate();return false;',
                ]
            ]
        );

        return $prampLoadTemplateButton->toHtml();
    }
}
