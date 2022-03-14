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
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Config\Source\AffiliateWindow;

/**
 * Integration status options.
 */
class CommissionGroup implements \Magento\Framework\Option\ArrayInterface
{
    const GROUP_CLIENT = 'client';
    const GROUP_PRODUCT = 'product';
    
    /**
     * Retrieve parameters options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            0 => ['value' => '', 'label' => __('None')],
            1 => ['value' => self::GROUP_CLIENT, 'label' => __('Client')],
            2 => ['value' => self::GROUP_PRODUCT, 'label' => __('Product')],
        ];
    }
}
