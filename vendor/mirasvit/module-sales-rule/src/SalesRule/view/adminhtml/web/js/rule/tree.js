define([
    'jquery',
    'underscore',
    'Mirasvit_SalesRule/js/utils',
    'uiElement',
    'mage/translate'
], function ($, _, utils, Element) {
    return Element.extend({
        defaults: {
            template: 'Mirasvit_SalesRule/rule/tree',
            
            imports: {
                rules: '${ $.provider }:rules'
            }
        },
        
        initialize: function () {
            this._super();
        },
        
        initObservable: function () {
            this._super()
                .observe({
                    rules: null
                });
            
            return this;
        }
        
        
    })
});