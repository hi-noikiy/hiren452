/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Ajaxcart v2.x.x
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    "jquery",
    "Plumrocket_Ajaxcart/js/prpopup",
    "Magento_Catalog/product/view/validation",
    "domReady!"
], function ($, prpopup) {

    var prajaxcartClass = function () {
        var $this = this;
        /**
         * @type {{
         *     url: {
         *         toCart: String,
         *         fromWishList: String,
         *     },
         *     formSelectors: String,
         *     popupSelector: String,
         *     asideSelector: String,
         *     modalClass: String,
         *     workMode: Number,
         *     buttonInnerHtml: String,
         *     notificationTemplate: String,
         *     isCategory: Boolean,
         *     categoryId: Number,
         * }}
         */
        $this.config = null;

        $this.setConfig = function (data) {
            $this.config = data;
            return this;
        };

        var isAutomaticMode;

        /**
         * Prepare add to cart buttons
         */
        $this.prepareForms = function () {
            isAutomaticMode = $this.config.workMode === 0;

            $($this.config.formSelectors).each(function (key, button) {
                var form;
                button = $(button);

                //skip some buttons
                if (! $this.filterButtons(button)) {
                    return this;
                }

                //check is this button or form
                if (!$this.checkIsInForm(button) && !button.find("button").length) {
                    $this.prepareFormData(button);
                } else if (isAutomaticMode) {
                    button = button.find("button");
                }

                if (button.length) {
                    if (!isAutomaticMode && button.hasClass('pr-manual-mode-flag')) {
                        return this;
                    }

                    var btnWrapper = '<div class="pac-btn-wrap"></div>';
                    button.attr("type", "button").attr("prajaxcart", "submit-button");

                    if (isAutomaticMode) {
                        button.addClass("pac-btn-cart")
                            .html($this.config.buttonInnerHtml)
                            .wrap(btnWrapper);
                    }

                    //fix for wishlist sidebar
                    if (button[0].hasAttribute("data-bind") && (button.attr('data-bind')).indexOf("add_to_cart_params") != -1) {
                        button.append('<span class="pac-wishlist-post" data-bind="attr: {' + "'data-post'" + ': add_to_cart_params}"></span>');
                        button.removeAttr("data-bind").addClass('addFromWishList');
                    }

                    if (button.is("a")) {
                        button.attr("href", "javascript:void(0)");
                    }

                    if (!isAutomaticMode) {
                        button.addClass('pr-manual-mode-flag');
                    }
                }
            });
            return this;
        };

        $this.checkIsInForm = function (button) {
            return button.parents('form[data-role="tocart-form"],form[id="product_addtocart_form"]').length;
        };

        /**
         * Skip some not supported buttons
         */
        $this.filterButtons = function (button) {
            //add different conditions to skip buttons
            return !(button.parents('.form.reorder').length
                || button.attr('data-role') == "all-tocart");

        };

        /**
         * Prepare serialized post-data in case selected button is not in form tag
         */
        $this.prepareFormData = function (button) {
            if (!button[0].hasAttribute("data-post")) {
                //fix from adding redirect to product page
                button.removeAttr("data-mage-init");
                var prpriceBox = button.parents('li.product-item').find('div[data-role="priceBox"]');
                var dataObject = {};
                if (!prpriceBox.length) {
                    return this;
                } else {
                    productId = prpriceBox.attr('data-product-id');
                    dataObject.data = {'product' : productId};
                }

            } else {
                dataObject = JSON.parse(button.attr("data-post"));
            }
            if (dataObject.action && (dataObject.action).indexOf('wishlist') != -1) {
                button.addClass('addFromWishList');
            }
            var dataSerialized = JSON.stringify(dataObject.data);
            button.attr("prajaxcart-data", dataSerialized)
                .removeAttr("data-post");

            return this;
        };

        $this.addToCart = function (button) {
            var url = $this.config.url.toCart;
            var parsedProductId = $this.parseProductId(button[0].form);

            if (button[0].hasAttribute("prajaxcart-data")) {
                var sendData = JSON.parse(button.attr("prajaxcart-data"));
                $this.sendAjax(sendData, url, button);
            } else {
                var form = button.closest('form'),
                    formId = form.attr('id'),
                    formData = new FormData(form[0]);
                    if (parsedProductId) {
                        formData.append('productId', parsedProductId);
                    }
                if (formId && ~formId.indexOf('product_addtocart_form')) {
                    if (form.valid()) {
                        $this.sendAjax(formData, url, button);
                    }
                } else {
                    $this.sendAjax(formData, url, button);
                }
            }
        };

        /**
         * The are two types of add from wish list buttons in magento
         * for that reason post-data serialized in different attributes
         *- on wishlist page with attr("prajaxcart-data")
         *- on wishlist sidebar(knockout) with attr("data-post") in span.pac-wishlist-post
         */
        $this.addFromWishList = function (button) {
            var url = $this.config.url.fromWishList;
            var spanPost = button.find("span.pac-wishlist-post");
            if (spanPost.length) {
                var postData = JSON.parse(spanPost.attr("data-post"));
                if (postData.data) {
                    $this.sendAjax(postData.data, url, button);
                }
            } else if (button[0].hasAttribute("prajaxcart-data")) {
                var sendData = JSON.parse(button.attr("prajaxcart-data"));
                $this.sendAjax(sendData, url, button);
            }
        };

        $this.sendAjax = function (data, url, button) {
            $this.beforeAjaxSend(button);
            data  = $this.prepareData(data);

            button.attr("disabled", "disabled");

            //for manual mode
            if (!isAutomaticMode) {
                $('body').trigger('processStart');
            }

            $.ajax({
                type: "POST",
                url: url,
                data: data,
                contentType: false,
                cache: false,
                processData:false,
                success: function (response) {
                    if (response.success == false && response.messages) {
                        if (response.messages.error) {
                            var html = $this.getNotificationHtml(response.messages.error);
                            $this.showPopup({'fullActionName' : response.fullActionName,}, html);
                            $('aside.pac-modal-popup').addClass('pac-error-msg').removeClass('prajaxcart_cart_addconfigure');
                        }
                    } else if (response.html) {

                        responseHtml = response.html;
                        var html = "";

                        $.each(responseHtml, function (i, cur) {
                            html += cur;
                        });

                        $this.showPopup({'fullActionName' : response.fullActionName,}, html);
                    } else if (response.success == false && response.action === 'redirect') {
                        window.location.href = atob(response.productUrl);
                    }
                },
                complete: function () {
                    button.removeAttr("disabled");
                    $this.afterAjaxSend(button);

                    //for manual mode
                    if (!isAutomaticMode) {
                        $('body').trigger('processStop');
                    }
                }
            });
        };

        $this.beforeAjaxSend = function (button) {
            $this.showLoader(true);
        };

        $this.afterAjaxSend = function (button) {
            $this.showLoader(false);
        };

        $this.prepareData = function (data) {
            var extendedData = {
                'isCategory' : $this.config.isCategory,
                'categoryId' : $this.config.categoryId,
            };
            if (data instanceof FormData) {
                for (var [key, item] of Object.entries(extendedData)) {
                    data.append(key, item);
                }
                return data;
            } else {
                if (!data.form_key) {
                    extendedData.form_key = $('input[name="form_key"]').val();
                }
                var completeFormData = $.extend(data, extendedData);
                var formData = new FormData();
                for ( var key in completeFormData ) {
                    formData.append(key, completeFormData[key]);
                }
                return formData;
            }
        };

        $this.showPopup = function (popupConfig, html) {
            var mainConfig = $this.config;
            $($this.config.popupSelector).html(html);
            prpopup.init($.extend(mainConfig, popupConfig));
        };

        $this.getNotificationHtml = function (messages) {
            var messageLayout = $this.config.notificationTemplate;
            var messagesHtml = '';
            messages.forEach(function (msg, i) {
                messagesHtml += '<p>' + msg + '</p>';
            });
            messageLayout = messageLayout.replace(/{text}/gi, messagesHtml);
            return messageLayout;
        };

        $this.showLoader = function (show) {
            var configurePopup = $('aside.pac-modal-popup.prajaxcart_cart_addconfigure');
            if (configurePopup.length) {
                if (show) {
                    configurePopup.addClass('pac-form-loading');
                } else {
                    configurePopup.removeClass('pac-form-loading');
                    /* fix for class attr when go from configure-popup to add_info-popup) */
                    if (configurePopup.hasClass('prajaxcart_cart_addinfo')) {
                        configurePopup.removeClass('prajaxcart_cart_addconfigure');
                    }
                }
            }
        };

        /**
         * @param {HTMLFormElement} form
         * @return {Number}
         */
        $this.parseProductId = function (form) {
            var result = form.action.match(/\/product\/(\d+)/);

            return result ? +result[1] : 0;
        };
    };

    return new prajaxcartClass();
});
