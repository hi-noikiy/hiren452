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
 * @package     Plumrocket_ShippingTracking
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ShippingTracking\Block\Adminhtml\System\Config\Form;

use Magento\Store\Model\ScopeInterface;

class Version extends \Plumrocket\Base\Block\Adminhtml\System\Config\Form\Version
{
    /**
     * Wiki link
     * @var string
     */
    protected $_wikiLink = 'http://wiki.plumrocket.com/Magento_2_Order_Status_and_Shipping_Tracking_v1.x_Extension';

    /**
     * Full module name
     * @var string
     */
    protected $_moduleName = 'Order Status  &amp; Shipping Tracking';
}