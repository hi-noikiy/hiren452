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
 * @package     Plumrocket Smtp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

require([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'domReady!'
], function($, alert) {
    "use strict";

    window.sendEmail = function(url) {
        var formData = {
            'host': $('#prsmtp_configuration_host').val(),
            'port': $('#prsmtp_configuration_port').val(),
            'encryption': $('#prsmtp_configuration_encryption').val(),
            'authentication': $('#prsmtp_configuration_authentication').val(),
            'username': $('#prsmtp_configuration_username').val(),
            'password': $('#prsmtp_configuration_password').val(),
            'template': $('#prsmtp_test_email_template').val(),
            'from': $('#prsmtp_test_email_from').val(),
            'to': $('#prsmtp_test_email_to').val()
        };

        $("body").trigger('processStart');
        $.post(url, formData, function(data) {
            data = JSON.parse($.trim(data));
            alert({
                title: $.mage.__('Send test email'),
                content: data.message
            });
        }).always(function() {
            $("body").trigger('processStop');
        }).fail(function() {
            alert({
                title: $.mage.__('Send test email'),
                content: $.mage.__('Something went wrong.')
            });
        });
    };
});
