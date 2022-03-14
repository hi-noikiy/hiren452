define([
    'jquery',
    'underscore',
    'uiRegistry'
], function ($, _, Registry) {
    'use strict';
    
    return {
        registry: function (name, callback, context) {
            var el = Registry.get(name);
            
            if (el) {
                callback.bind(context)(el);
            } else {
                var i = setInterval(function () {
                    var el = Registry.get(name);
                    
                    if (el) {
                        clearInterval(i);
                        callback.bind(context)(el);
                    }
                }, 50);
            }
        }
    }
});