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
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

/**
 * @since 1.0.1
 */
require([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'domReady!'
], function ($, alert) {
    "use strict";

    window.sendPrAmpTestEmail = function (url) {
        $.ajax({
            url: url,
            data: {to: $('#prampemail_test_email_to').val()},
            type: 'POST',
            cache: true,
            dataType: 'json',
            showLoader: true,

            /**
             * Response handler
             * @param {Object} data
             */
            success: function (data) {
                alert({
                    title: $.mage.__('Send test email'),
                    content: data.message,
                });
            },
            error: function () {
                alert({
                    title: $.mage.__('Send test email'),
                    content: $.mage.__('Something went wrong. Please review the log for details or try later.'),
                });
            }
        });
    };
});
