define([
    'jquery',
], function ($, reload) {
    'use strict';

    return function (widget) {
        $.widget('mage.configurable', widget, {
            options: {
                priceHolderSelector: $('.price-box').length > 1 ? '#pac_product_addtocart_form .price-box' : '.price-box',
            },
        });

        return $.mage.configurable;
    }
});
