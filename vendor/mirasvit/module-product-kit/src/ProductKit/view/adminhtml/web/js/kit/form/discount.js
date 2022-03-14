define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Mirasvit_ProductKit/kit/form/discount',

            imports: {
                discountAmount:      '${ $.provider }:${ $.parentScope }.discount_amount',
                discountType:        '${ $.provider }:${ $.parentScope }.discount_type',
                generalDiscountType: '${ $.provider }:data.general_discount_type'
            },
            exports: {
                discountAmount: '${ $.provider }:${ $.parentScope }.discount_amount',
                discountType:   '${ $.provider }:${ $.parentScope }.discount_type'
            },
            listens: {
                generalDiscountType: 'handleGeneralDiscountType'
            },

            options: []
        },

        initialize: function () {
            this._super();

            if (!this.discountType()) {
                this.discountType('percentage');
            }

            if (!this.discountAmount()) {
                this.discountAmount(0);
            }

            return this;
        },

        initObservable: function () {
            return this._super()
                .observe('discountAmount')
                .observe('discountType')
                .observe('generalDiscountType');
        },

        isNonComplexDiscount: function () {
            return this.generalDiscountType() !== 'complex';
        },

        handleGeneralDiscountType: function () {
            if (this.generalDiscountType() === 'complex') {

            } else {
                this.discountType(this.generalDiscountType());
            }
        }
    });
});
