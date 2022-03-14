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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    "jquery",
    "ko"
], function ($, ko) {
    'use strict';

    var mixin = {
        minicart: $('[data-block="minicart"]'),

        initialize: function () {
            var self = this;

            if (window.prAjaxCartEnabled) {
                this.minicart.on('click', '[data-action="close"]', function (event) {
                    event.stopPropagation();
                    var rightbarBlock = self.minicart.find('[data-role="rightbar"]');
                    if (rightbarBlock.length) {
                        rightbarBlock.rightbar('close');
                    }
                });
            }

            return this._super();
        },

        closeRightbar: function () {
            var self = this;
            this.minicart.on('click', '[data-action="close"]', function (event) {
                event.stopPropagation();
                self.minicart.find('[data-role="rightbar"]').rightbar('close');
            });

            return true;
        },

        update: function (updatedCart) {
            this._super();

            if (!window.prAjaxCartEnabled) {
                return;
            }

            var updateItems = [];

            if (window.prAjaxCartProductQty == 1
                && ($('body').hasClass('catalog-category-view')
                || $('body').hasClass('cms-index-index')
                || $('body').hasClass('catalogsearch-result-index'))
            ) {
                if (typeof updatedCart.items !== 'undefined') {
                    updatedCart.items.forEach(function (newItem) {
                        var updated = false;

                        if (updateItems.length > 0) {
                            updateItems.forEach(function (savedItem) {
                                if (savedItem.id == newItem.product_id) {
                                    updateQty(savedItem, newItem.qty);
                                    updated = true;
                                    return false;
                                }
                            });
                            if (!updated) {
                                updateItems = addItem(newItem, updateItems);
                            }
                        } else {
                            updateItems = addItem(newItem, updateItems);
                        }
                    });
                }

                renderQty(updateItems);
            }

            function addItem(item, items) {
                var itemId = item.product_id ? item.product_id : item.item_id;
                items.push({id: itemId, qty: item.qty});
                return items;
            }

            function updateQty(item, qty) {
                item.qty += qty;
                return item;
            }

            function renderQty(updateItems) {
                $('.pac-qty-cart').remove();

                $.each(updateItems, function (index, item) {
                    var customSelector = window.prAjaxCartProductQtySelector;
                    var searhElement = $('body').find('.data-prac-' + item.id).closest('.' + customSelector);

                    if (searhElement.length > 0) {
                        var qtyClasses = 'pac-qty-cart';

                        if (searhElement.find('.prcr-product-reserved-text').length > 0) {
                            qtyClasses += ' pac-qty-cart-top';
                        }

                        var qtyElement = '<div class="' + qtyClasses + '" data-qty="' + item.id + '">' + item.qty + '</div>';
                        searhElement.prepend(qtyElement);
                    }
                });
            }
        }
    };

    return function (target) { // target == Result that Magento_Checkout/js/view/minicart returns.
        return target.extend(mixin); // new result
    };
});
