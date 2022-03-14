define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/components/fieldset'
], function ($, __, registry, Fieldset) {
    'use strict';
    return Fieldset.extend({
        defaults: {
            imports: {
                isSmart:          '${ $.provider }:data.is_smart',
                smartItemsNumber: '${ $.provider }:data.smart_items_number'
                
            },
            
            listens: {
                isSmart:          'updateVisibility',
                smartItemsNumber: 'updateVisibility'
            }
        },
        
        initialize: function () {
            this._super();
            
            setInterval(this.updateVisibility.bind(this), 100);
        },
        
        updateVisibility: function () {
            if (!parseInt(this.isSmart)) {
                this.toggle(false);
                return;
            }
            
            if (this.position > this.smartItemsNumber) {
                this.toggle(false);
            } else {
                this.toggle(true);
            }
        },
        
        toggle: function (visible) {
            this.visible(visible);
            
            if (this.elems().length === 0) {
                return
            }
            
            _.each(this.elems(), function (el) {
                if (el.validation && el.validation['required-entry']) {
                    el.visible(visible);
                }
            })
        }
    });
});
