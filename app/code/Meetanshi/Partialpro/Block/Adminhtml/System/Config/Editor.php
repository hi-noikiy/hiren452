<?php

namespace Meetanshi\Partialpro\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;

class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $wysiwygConfig;

    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    )
    {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setWysiwyg(true);
        $confgiData = $this->wysiwygConfig->getConfig($element);
        $confgiData->setplugins([]);
        $confgiData->setadd_variables(0);
        $confgiData->setadd_widgets(0);

        $element->setConfig($confgiData);
        return parent::_getElementHtml($element);
    }
}