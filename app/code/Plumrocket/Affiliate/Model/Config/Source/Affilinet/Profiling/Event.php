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

namespace Plumrocket\Affiliate\Model\Config\Source\Affilinet\Profiling;

use Plumrocket\Affiliate\Model\Affiliate\Affilinet as Affilinet;

/**
 * Integration status options.
 */
class Event implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve parameters options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            Affilinet::HOME_PAGE => [
                'value' => 'PageView',
                'label' => __('Page View'),
                'required' => [
                    'page_name',
                ],
            ],
            Affilinet::CATEGORY_PAGE => [
                'value' => 'CategoryView',
                'label' => __('Category View'),
                'required' => [
                    'category_name',
                    'category_id',
                ],
            ],
            Affilinet::PRODUCT_PAGE => [
                'value' => 'ProductView',
                'label' => __('Product View'),
                'required' => [
                    'currency',
                    'product_id',
                    'product_name',
                    'product_price',
                    'product_category',
                ],
            ],
            Affilinet::CART_PAGE => [
                'value' => 'CartView',
                'label' => __('Cart View'),
                'required' => [
                    'currency',
                    'products',
                ],
            ],
            Affilinet::CHECKOUT_SUCCESS_PAGE => [
                'value' => 'Checkout',
                'label' => __('Checkout Success Page'),
                'required' => [
                    'currency',
                    'order_id',
                    'order_total_gross_price',
                    'order_total_items',
                    'products',
                ],
            ],
            Affilinet::SEARCH_RESULT_PAGE => [
                'value' => 'Search',
                'label' => __('Search Result Page'),
                'required' => [
                    'search_keywords',
                    'products',
                ],
            ],
        ];
    }
}
