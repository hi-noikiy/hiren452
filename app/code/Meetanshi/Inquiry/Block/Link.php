<?php

namespace Meetanshi\Inquiry\Block;

use Magento\Framework\View\Element\Template\Context;
use Meetanshi\Inquiry\Helper\Data;

class Link extends \Magento\Framework\View\Element\Html\Link
{
    protected $helper;

    public function __construct(Context $context, array $data = [], Data $helper)
    {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        if (!$this->_scopeConfig->isSetFlag('dealer_inquiry/settings/enable')) {
            return '';
        }
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }
        $url = $this->_storeManager->getStore()->getBaseUrl() . $this->helper->getUrlKey();
        return '<li><a href="' . $url . '" >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
    }
}
