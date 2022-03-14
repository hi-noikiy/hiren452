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
 * @package     Plumrocket_Estimateddelivery
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Estimateddelivery\Plugin;

use Plumrocket\Estimateddelivery\Model\Config\Source\Position;

class QuoteTotalsItemPlugin
{
    protected $_helper;
    protected $_productHelper;
    protected $_layout;
    protected $_session;

    /**
     * QuoteTotalsItemPlugin constructor.
     *
     * @param \Plumrocket\Estimateddelivery\Helper\Data $helper
     * @param \Plumrocket\Estimateddelivery\Helper\Product $productHelper
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        \Plumrocket\Estimateddelivery\Helper\Data $helper,
        \Plumrocket\Estimateddelivery\Helper\Product $productHelper,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Checkout\Model\Session $session
    ) {
        $this->_helper = $helper;
        $this->_productHelper = $productHelper;
        $this->_layout = $layout;
        $this->_session = $session;
    }

    public function afterGetOptions($subject, $options)
    {
        if ($this->_helper->moduleEnabled()) {
            switch (true) {
                case $this->_helper->showPosition(Position::CHECKOUT):
                    if ($_options = $this->_getEstimatedDeliveryOptions($this->_session->getQuote()->getItemById($subject->getItemId()))) {
                        if (is_string($options)) {
                            $options = json_decode($options, true);
                        }
                        if (!is_array($options)) {
                            $options = [];
                        }
                        $options = json_encode(array_merge($options, $_options));
                    }
                    break;
            }
        }

        return $options;
    }

    protected function _getEstimatedDeliveryOptions($item)
    {
        $product = $item->getProduct();
        $this->_productHelper->setProduct($product);
        $options = $this->_productHelper->getOptions($item, false);
        return array_values($options);
    }
}
