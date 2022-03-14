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

namespace Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking;

/**
 * Integration status options.
 */
class Parameter implements \Magento\Framework\Option\ArrayInterface
{
    const MAX_COUNT = 2;

    const PAYMENT_METHOD    = 'payment_method';
    const SHIPPING_METHOD   = 'shipping_method';

    /**
     * Collection of tracking parameters
     * @var nulll | array
     */
    protected $_options;

    /**
     * Retrieve parameters options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (null === $this->_options) {
            $this->_options = [
                [
                    'value' => self::PAYMENT_METHOD,
                    'label' => __('Payment method'),
                ],
                [
                    'value' => self::SHIPPING_METHOD,
                    'label' => __('Shipping method'),
                ]
            ];

            array_unshift($this->_options, [
                'value' => '',
                'label' => __('-- Please Select --'),
            ]);
        }
        return $this->_options;
    }
}
