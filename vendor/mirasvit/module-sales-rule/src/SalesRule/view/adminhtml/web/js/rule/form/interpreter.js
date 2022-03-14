define([
    'jquery',
    'underscore',
    'Mirasvit_SalesRule/js/utils',
    'uiElement',
    'uiRegistry',
    'mage/translate'
], function ($, _, utils, Element, Registry) {
    return Element.extend({
        defaults: {
            template: 'Mirasvit_SalesRule/rule/form/interpreter',
            
            imports: {
                conditions:          '${ $.provider }:data.conditions_serialized',
                simpleAction:        '${ $.provider }:data.simple_action',
                discountAmount:      '${ $.provider }:data.discount_amount',
                discountQty:         '${ $.provider }:data.discount_qty',
                discountStep:        '${ $.provider }:data.discount_step',
                applyToShipping:     '${ $.provider }:data.apply_to_shipping',
                stopRulesProcessing: '${ $.provider }:data.stop_rules_processing'
            },
            
            listens: {
                simpleAction: 'updateSimpleActionLabel'
            }
        },
        
        initialize: function () {
            this._super();
        },
        
        initObservable: function () {
            this._super()
                .observe({
                    conditions:          null,
                    simpleAction:        null,
                    simpleActionLabel:   null,
                    discountAmount:      null,
                    discountQty:         null,
                    discountStep:        null,
                    applyToShipping:     null,
                    stopRulesProcessing: null
                });
            
            return this;
        },
        
        updateSimpleActionLabel: function () {
            
            utils.registry(this.parentName + '.actions.simple_action', function (el) {
                var opt = _.findWhere(el.options(), {value: this.simpleAction()});
                this.simpleActionLabel(opt.label);
            }, this);
        },
        
        money: function (val) {
            val = _.isFunction(val) ? val() : val;
            
            return val ? '$' + val : '-';
        },
        
        qty: function (val) {
            val = _.isFunction(val) ? val() : val;
            
            return val ? val + 'x' : '-';
        },
        
        percent: function (val) {
            val = _.isFunction(val) ? val() : val;
            
            return val ? val + '%' : '-';
        }
    })
});