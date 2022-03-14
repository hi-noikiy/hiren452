define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'

], function (ko, Component, quote, priceUtils, totals) {
    'use strict';
    var show_hide_partial_blockConfig = window.checkoutConfig.show_hide_partial_block;
    var fee_label = window.checkoutConfig.fee_label;
    var amt_pay_now_label = window.checkoutConfig.amt_pay_now_label;
    var amt_pay_later_label = window.checkoutConfig.amt_pay_later_label;

    return Component.extend({

        totals: quote.getTotals(),
        canVisiblePartialBlock: show_hide_partial_blockConfig,
        getPartialInstallmentFeeLabel: ko.observable(fee_label),
        getAmtPayNowLabel: ko.observable(amt_pay_now_label),
        getAmtPayLaterLabel: ko.observable(amt_pay_later_label),
        isDisplayInstallmentFee: function () {
            return this.getValueInstallment() != 0;
        },
        getValueInstallment: function () {
            var price = 0;
            if (this.totals() && totals.getSegment('partial_installment_fee')) {
                price = totals.getSegment('partial_installment_fee').value;
            }
            return price;
        },
        isDisplayed: function () {
            return this.getValue() != 0;
        },
        getValue: function () {
            var price = 0;
            if (this.totals() && totals.getSegment('partial_pay_now')) {
                price = totals.getSegment('partial_pay_now').value;
            }
            return price;
        },
        getInstallmentFee: function () {
            var price = 0;
            if (this.totals() && totals.getSegment('partial_installment_fee')) {
                price = totals.getSegment('partial_installment_fee').value;
            }
            return ko.observable(priceUtils.formatPrice(price, quote.getPriceFormat()));
        },
        getAmtPayLaterPrice: function () {
            var price = 0;
            if (this.totals() && totals.getSegment('partial_pay_later')) {
                price = totals.getSegment('partial_pay_later').value;
            }
            return ko.observable(priceUtils.formatPrice(price, quote.getPriceFormat()));
        },
        getAmtPayNowPrice: function () {
            var price = 0;
            if (this.totals() && totals.getSegment('partial_pay_now')) {
                price = totals.getSegment('partial_pay_now').value;
            }
            return ko.observable(priceUtils.formatPrice(price, quote.getPriceFormat()));
        }
    });
});