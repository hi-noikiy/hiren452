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

namespace Plumrocket\Estimateddelivery\Block;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_helper;
    protected $_productHelper;
    protected $request;

    /**
     * Product constructor.
     *
     * @param \Plumrocket\Estimateddelivery\Helper\Data        $helper
     * @param \Plumrocket\Estimateddelivery\Helper\Product     $productHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Request\Http              $request
     * @param array                                            $data
     */
    public function __construct(
        \Plumrocket\Estimateddelivery\Helper\Data $helper,
        \Plumrocket\Estimateddelivery\Helper\Product $productHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->_helper = $helper;
        $this->_productHelper = $productHelper;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_productHelper->isEnabled();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (! $this->isEnabled()) {
            $this->setTemplate('empty.phtml');
        }
        return parent::_toHtml();
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        return $this->_helper->showPosition($this->request->getControllerName())
            && ($this->hasDeliveryDate() || $this->hasShippingDate());
    }

    /**
     * @param $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->_productHelper->setCategory($category);
        return $this;
    }

    /**
     * @param      $product
     * @param null $orderItem
     * @return $this
     */
    public function setProduct($product, $orderItem = null)
    {
        $this->_productHelper->setProduct($product, $orderItem);
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->_productHelper->reset();
        return $this;
    }

    public function getProduct()
    {
        return $this->_productHelper->getProduct();
    }

    public function getCategory()
    {
        return $this->_productHelper->getCategory();
    }

    public function hasDeliveryDate()
    {
        return $this->_productHelper->hasDeliveryDate();
    }

    public function hasShippingDate()
    {
        return $this->_productHelper->hasShippingDate();
    }
}
