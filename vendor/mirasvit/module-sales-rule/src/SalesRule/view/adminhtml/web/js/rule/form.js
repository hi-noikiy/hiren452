define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'mage/translate'
], function ($, _, Form) {
    return Form.extend({
        defaults: {
            imports: {
                simpleAction: '${ $.provider }:data.simple_action',
                couponType:   '${ $.provider }:data.coupon_type'
            },
            
            listens: {
                simpleAction: 'updateView',
                couponType:   'updateCouponView'
            }
        },
        
        initialize: function () {
            this._super();
            
            _.bindAll(
                this,
                'updateView',
                'updateCouponView'
            );
            
            setInterval(this.updateView, 100);
        },
        
        updateView: function () {
            this.label('discount_amount', 'Discount Amount')
                .label('discount_step', 'Discount Qty Step (Buy X)')
                .label('discount_qty', 'Maximum Qty Discount is Applied To')
                .label('conditions', 'Apply the rule only if the following conditions are met (leave blank for all products).')
                .label('actions', 'Apply the rule only to cart items matching the following conditions (leave blank for all items).');
            
            this.show('discount_amount')
                .show('discount_step')
                .show('discount_qty')
                .show('simple_free_shipping');
            
            switch (this.simpleAction) {
                case 'mst_each_x_m_get_y_m':
                    this.label('discount_amount', 'Discount Amount $Y')
                        .label('discount_step', 'Spend $X');
                    
                    break;
                
                case 'mst_buy_x_get_y':
                    this.label('discount_amount', 'Discount Amount (in %)')
                        .label('discount_qty', 'Maximum Qty to Apply')
                        .label('conditions', 'Apply the rule only if the following conditions are met (must follow X product).')
                        .label('actions', 'Y product conditions');
    
                    this.hide('discount_step');
    
                    break;
                    
                case 'mst_buy_x_get_amount_y':
                    this.label('discount_amount', 'Discount Amount (in $)')
                        .label('discount_qty', 'Maximum Qty to Apply')
                        .label('conditions', 'Apply the rule only if the following conditions are met (must follow X product).')
                        .label('actions', 'Y product conditions');
                    
                    this.hide('discount_step');
                    
                    break;
                
                case 'mst_most_expensive':
                case 'mst_most_cheapest':
                case 'mst_except_expensive':
                    this.label('discount_amount', 'Discount Amount (in %)')
                        .label('discount_qty', 'Maximum Qty to Apply');
                    
                    this.hide('discount_step')
                        .hide('simple_free_shipping');
                    
                    break;
            }
        },
        
        updateCouponView: function () {
            switch (this.couponType) {
                case 1:
                    this.hide('coupon_success_message')
                        .hide('coupon_error_message');
                    break;
                
                case 2:
                    this.show('coupon_success_message')
                        .show('coupon_error_message');
                    break;
            }
        },
        
        label: function (field, text) {
            var selector = '[data-index="' + field + '"] label span';
            
            if (field === 'conditions') {
                selector = $('[data-index="conditions"] .rule-tree legend span');
            } else if (field === 'actions') {
                selector = $('[data-index="actions"] .rule-tree legend span');
            }
            
            var $el = $(selector);
            $el.text($.mage.__(text));
            
            return this;
        },
        
        hide: function (field) {
            var $el = $('[data-index="' + field + '"]');
            $el.hide();
            
            return this;
        },
        
        show: function (field) {
            var $el = $('[data-index="' + field + '"]');
            $el.show();
            
            return this;
        }
    });
});