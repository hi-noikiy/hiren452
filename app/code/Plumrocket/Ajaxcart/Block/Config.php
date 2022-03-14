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
 * @package     Plumrocket Ajaxcart v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Block;

use Plumrocket\Ajaxcart\Helper\Data as DataHelper;

class Config extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Plumrocket\Ajaxcart\Helper\Data|DataHelper
     */
    protected $dataHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * @return int
     */
    public function showProductQty()
    {
        return $this->dataHelper->showProductQtyCartOnProductList();
    }

    /**
     * @return string
     */
    public function getQtyBlockClassName()
    {
        return $this->dataHelper->getQtyBlockClass();
    }

    /**
     * @return string
     */
    public function isAjaxcartEnabled()
    {
        return $this->dataHelper->moduleEnabled() ? 'true' : 'false';
    }
}
