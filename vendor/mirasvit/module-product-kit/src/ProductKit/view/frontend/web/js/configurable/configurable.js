define([
    'jquery'
], function ($) {
    'use strict';
    
    return function (widget) {
        $.widget('mage.configurable', widget, {
            _initializeOptions: function () {
                if ($(this.element).parent('.product-add-form').length && !this.options.spConfig.containerId) {
                    this.options.spConfig.containerId = '#' + $(this.element).attr('id');
                    this.options.spConfig.images = [];
                }
                
                return this._super();
            }
        });
        
        return $.mage.configurable;
    }
});
