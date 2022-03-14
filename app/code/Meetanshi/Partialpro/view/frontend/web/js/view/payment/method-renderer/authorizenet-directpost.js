define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/iframe',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/set-payment-information',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function ($,
              Component,
              fullScreenLoader,
              setPaymentInformationAction,
              additionalValidators) {
        'use strict';

        var receiveUrl = BASE_URL.concat('partialpayment/authorize/applyAutoCapture');
        var isPartialOrder;
        $.ajax({
            type: "POST",
            url: receiveUrl,
            success: function (result) {
                isPartialOrder = result;
            },
            async: false
        });
        return Component.extend({
            defaults: {
                template: 'Magento_Authorizenet/payment/authorizenet-directpost',
                timeoutMessage: 'Sorry, but something went wrong. Please contact the seller.'
            },
            placeOrderHandler: null,
            validateHandler: null,
            isSuccess: true,
            customerProfileId: '',
            paymentProfileId: '',

            setPlaceOrderHandler: function (handler) {
                this.placeOrderHandler = handler;
            },

            setValidateHandler: function (handler) {
                this.validateHandler = handler;
            },

            context: function () {
                return this;
            },

            isShowLegend: function () {
                return true;
            },

            getCode: function () {
                return 'authorizenet_directpost';
            },

            isActive: function () {
                return true;
            },

            getTimeoutTime: function () {
                return 99999;
            },

            placeOrder: function () {
                var self = this;
                if (this.validateHandler() && additionalValidators.validate()) {
                    fullScreenLoader.startLoader();
                    this.isPlaceOrderActionAllowed(false);

                    if (isPartialOrder) {
                        if ($('#authorizenet_directpost_cc_cid').length) {
                            var result = {
                                "ccNumber": $('#authorizenet_directpost_cc_number').val(),
                                "expMonth": $('#authorizenet_directpost_expiration').val(),
                                "expYear": $('#authorizenet_directpost_expiration_yr').val(),
                                "ccId": $('#authorizenet_directpost_cc_cid').val()
                            };
                        } else {
                            var result = {
                                "ccNumber": $('#authorizenet_directpost_cc_number').val(),
                                "expMonth": $('#authorizenet_directpost_expiration').val(),
                                "expYear": $('#authorizenet_directpost_expiration_yr').val()
                            };
                        }
                        var requestUrl = BASE_URL.concat('partialpayment/authorize/createProfile');
                        $.ajax({
                            type: "POST",
                            url: requestUrl,
                            data: {
                                result: result
                            },
                            success: function (res) {
                                if (res.result === false) {
                                    fullScreenLoader.stopLoader();
                                    self.isPlaceOrderActionAllowed(true);
                                    self.isSuccess = false;
                                } else {
                                    self.customerProfileId = res.customerProfileId;
                                    self.paymentProfileId = res.paymentProfileId;
                                }
                            },
                            async: false
                        });
                    }

                    if (this.isSuccess) {
                        $.when(
                            setPaymentInformationAction(
                                this.messageContainer,
                                {
                                    method: this.getCode()
                                }
                            )
                        ).done(this.done.bind(this))
                            .fail(this.fail.bind(this));

                        this.initTimeoutHandler();
                    }
                }
            },
            done: function () {
                this.placeOrderHandler().fail(function () {
                    fullScreenLoader.stopLoader();
                });
                return this;
            },

            fail: function () {
                fullScreenLoader.stopLoader();
                this.isPlaceOrderActionAllowed(true);

                return this;
            },

            initTimeoutHandler: function () {
                this.timeoutId = setTimeout(
                    this.timeoutHandler.bind(this),
                    this.getTimeoutTime()
                );

                $(window).off('clearTimeout')
                    .on('clearTimeout', this.clearTimeout.bind(this));
            },

            clearTimeout: function () {
                clearTimeout(this.timeoutId);

                return this;
            },

            timeoutHandler: function () {
                this.clearTimeout();
                alert(
                    {
                        content: this.getTimeoutMessage(),
                        actions: {
                            always: this.alertActionHandler.bind(this)
                        }
                    }
                );

                this.fail();
            },

            getTimeoutMessage: function () {
                return $t(this.timeoutMessage);
            }
        });
    }
);