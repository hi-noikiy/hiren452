define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            _RenderControls: function () {
                var tmp = this.inProductList;
                if (this.element.closest('.mst-product_kit__cart-item').length) {
                    this.inProductList = false;
                }

                this._super();

                this.inProductList = tmp;

                return this;
            }
        });

        return $.mage.SwatchRenderer;
    }
});
