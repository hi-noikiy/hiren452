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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model;

class Includeon extends \Magento\Framework\Model\AbstractModel
{
    const ALL_PAGES = 'all';
    const REGISTRATION_SUCCESS_PAGES = 'registration_success_pages';
    const LOGIN_SUCCESS_PAGES = 'login_success_pages';
    const HOME_PAGE = 'home_page';
    const PRODUCT_PAGE = 'product_page';
    const CATEGORY_PAGE = 'category_page';
    const CART_PAGE = 'cart_page';
    const ONE_PAGE_CHECKOUT = 'one_page_chackout';
    const CHECKOUT_SUCCESS_PAGE = 'checkout_success';
    const SEARCH_RESULT_PAGE = 'catalogsearch_result_page';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Plumrocket\Affiliate\Model\ResourceModel\Includeon');
    }

}
