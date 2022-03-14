/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/full-screen-loader',
    'underscore'
], function (quote, urlBuilder, storage, errorProcessor, customer, fullScreenLoader, _) {
    'use strict';

    /**
     * Filter template data.
     *
     * @param {Object|Array} data
     */
    var filterTemplateData = function (data) {
        return _.each(data, function (value, key, list) {
            if (_.isArray(value) || _.isObject(value)) {
                list[key] = filterTemplateData(value);
            }

            if (key === '__disableTmpl') {
                delete list[key];
            }
        });
    };

    return function (data) {
        if (_.isEmpty(data)) {
          return;
        }
        var serviceUrl,
            payload;

        data = filterTemplateData(data);
        payload = {
            cartId: quote.getQuoteId(),
            data: data
        };

        /**
         * Checkout for guest and registered customer.
         */
        if (!customer.isLoggedIn()) {
            serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/request-zillion-insurance', {
                cartId: quote.getQuoteId()
            });
        } else {
            serviceUrl = urlBuilder.createUrl('/carts/mine/request-zillion-insurance', {});
        }

        fullScreenLoader.startLoader();

        return storage.post(
            serviceUrl, JSON.stringify(payload)
        ).fail(
            function (response) {
                console.error(response);
                fullScreenLoader.stopLoader();
            }
        ).always(
            function () {
                fullScreenLoader.stopLoader();
            }
        );
    };
});
