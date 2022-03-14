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
define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/confirm',
], function ($, customerData, confirm) {
    'use strict';

    return function (widget) {
        $.widget('mage.sidebar', widget, {
            /**
             * {@inheritdoc}
             */
            _initContent: function () {
                this.isRightbar = $('.block.rightbar').length;
                this._super();

                if (this.isRightbar) {
                    $('.minicart-items-wrapper').css('max-height', 'none');
                }
            },

            /**
             * {@inheritdoc}
             */
            _calcHeight: function () {
                if (this.isRightbar) {
                    return;
                }

                this._super();
            }
        });

        return $.mage.sidebar;
    }
});
