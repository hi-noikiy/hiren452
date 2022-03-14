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
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    'ko',
    'uiComponent',
    'jquery',
    'mage/translate'
], function (ko, Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Plumrocket_Newsletterpopup/integration/custom-fields'
        },

        customFields: ko.observable(false),

        /** @inheritdoc */
        initialize: function () {
            this._super();

            if (typeof window.prnewsletterpopup === 'undefined') {
                window.prnewsletterpopup = {loadCustomFields: {}};
            }

            window.prnewsletterpopup.loadCustomFields.constantcontact = this.loadCustomFields.bind(this);
        },

        loadCustomFields: function (url) {
            var self = this;

            $.ajax({
                url: url,
                data: {},
                method: 'GET',
                showLoader: true,
                dataType: 'json',
                success: function (response) {
                    if ('success' !== response.result) {
                        return false;
                    }

                    if (0 !== response.info.length) {
                        self.customFields(response.info);
                    } else {
                        self.customFields([]);
                    }
                },
                error: function (response) {
                    self.showErrorMessage(__('Something went wrong.') + '' + response.status + ':' + response.statusText);
                }
            });
        }
    });
});
