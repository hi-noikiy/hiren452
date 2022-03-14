define([
    "jquery",
    "underscore",
    'mage/translate',
    'mage/url',
    'Magento_Catalog/js/price-utils',
    'Magento_Catalog/product/view/validation'
], function ($, _, $t, mageUrl, priceUtils) {
    $.widget('mirasvit.kit', {
        $el: null,

        priceSelector:         '[data-element=price]',
        priceDiscountSelector: '[data-element=price-discount]',
        fullPriceSelector:     '[data-element=full-price]',
        buttonSelector:        '[data-element=cartButton]',
        cancelButtonSelector:  '[data-element=cancelButton]',
        kitItemSelector:       '[data-element=kit-item]',
        itemDiscountSelector:  '[data-element=kit-item-discount]',
        itemNewPriceSelector:  '[data-element=kit-item-newPrice]',
        downSelector:          '[data-element=itemsSet-pager-down]',
        upSelector:            '[data-element=itemsSet-pager-up]',
        productBlockSelector:  '[data-element=product-block]',

        options: {
            base_id:         '',
            title:           '',
            label:           '',
            combinations:    [],
            add_url:         '',
            action_name:     '',
            should_redirect: false
        },

        _create: function () {
            this.$el = $(this.element);

            this.refresh();

            this.$el.on('click', this.refresh.bind(this));

            $(this.upSelector, this.$el).on('click', this.changeProduct);
            $(this.downSelector, this.$el).on('click', this.changeProduct);

            $(this.buttonSelector, this.$el).on('click', this.addToCart.bind(this));
            $(this.cancelButtonSelector, this.$el).on('click', this.returnToSelect.bind(this));
        },

        refresh: function () {
            this.reCalculate();

            _.each(this.getAllItemIds(), function (id) {
                const $item = this.$item(id);

                if (this.isItemSelected(id)) {
                    $item.removeClass('_disabled');
                } else {
                    $item.addClass('_disabled');
                }
            }.bind(this));
        },

        getCurrentCombination: function () {
            const priceIndex = this.getSelectedCombinationHash();

            if (this.options.combinations[priceIndex] === undefined) {
                return false
            }

            return this.options.combinations[priceIndex];
        },

        reCalculate: function () {
            this.reset();

            const combination = this.getCurrentCombination();

            if (!combination) {
                return false;
            }

            const finalPrice    = combination['discounted_price'];
            const fullPrice     = combination['full_price'];
            const discountPrice = fullPrice - finalPrice;

            if (finalPrice != fullPrice) {
                $(this.fullPriceSelector, this.$el).html(priceUtils.formatPrice(fullPrice, this.options.price_format));
            }
            $(this.priceSelector, this.$el).html(priceUtils.formatPrice(finalPrice, this.options.price_format));
            $(this.priceDiscountSelector, this.$el).html(priceUtils.formatPrice(discountPrice, this.options.price_format));

            _.each(combination.items, function (item) {
                const itemId = item['item_id']

                $(this.itemDiscountSelector, this.$item(itemId)).html(item['discount_html']);
                $(this.itemNewPriceSelector, this.$item(itemId)).html(item['discounted_price_html']);
            }.bind(this));
        },

        reset: function () {
            $(this.priceSelector, this.$el).html('');
            $(this.fullPriceSelector, this.$el).html('');
            $(this.priceDiscountSelector, this.$el).html('');

            $(this.itemDiscountSelector, this.$items()).html('');
            $(this.itemNewPriceSelector, this.$items()).html('');
        },

        kitAddToCart: function () {
            $('.content-' + this.options.base_id).html('');

            this.addToCart();
        },

        returnToSelect: function () {
            $('[data-element=cartContent]', this.$el).html('');
            this.$el.removeClass('_cart');
        },

        addToCart: function () {
            const combination = this.getCurrentCombination();
            if (!combination) {
                return false;
            }
            var selectedCombination      = this.getSelectedCombinationHash();
            var selectedQuoteCombination = this.getSelectedCombinationQuoteHash();

            var productsData = {};
            var productForms = $('form', $('[data-element=cartContent]', this.$el));
            var requestData = {};

            productsData[this.options.kit_id] = {};
            var kitData = {};
            _.each(combination['items'], function (item) {
                kitData[item['product_id']] = {
                    'kit_id':     this.options.kit_id,
                    'product_id': item['product_id'],
                    'item_id':    item['item_id'],
                    'position':   item['position']
                };
            }.bind(this));

            if (!productForms.length) {
                productsData[this.options.kit_id] = kitData;
                requestData = {
                    products: productsData
                };
                // add uniq hash for each kit
                this.options.hash = btoa(this.options.kit_id + Date.now());
            } else {
                var isValidForms = true;

                for (var index = 0; index < productForms.length; index++) {
                    if (typeof productForms[index] != 'undefined') {
                        $(productForms[index]).append('<input type="hidden" name="kit_id" value="' + this.options.kit_id + '">');
                        if ($('[name="product"]', productForms[index]).length) {
                            var productId = $('[name="product"]', productForms[index]).val();
                            $(productForms[index]).append('<input type="hidden" name="item_id" value="' + kitData[productId]['item_id'] + '">');
                            $(productForms[index]).append('<input type="hidden" name="position" value="' + kitData[productId]['position'] + '">');
                        }
                        var productFormValidator = $(productForms[index]).validation({radioCheckboxClosest: '.nested'});
                        if (!productFormValidator.valid()) {
                            isValidForms = false;
                        }
                        productsData[this.options.kit_id][index] = $(productForms[index]).serialize();
                    }
                }
                if (!isValidForms) {
                    return;
                }
                requestData = {
                    forms: productsData
                };
            }
            requestData.hash = this.options.hash;

            requestData.selectedCombination      = selectedCombination;
            requestData.selectedQuoteCombination = selectedQuoteCombination;

            const $cartContent = $('[data-element=cartContent]', this.$el);

            $.ajax({
                type:     "POST",
                url:      this.options.add_url,
                data:     requestData,
                dataType: 'json',

                beforeSend: function () {
                    this.toggleAddToCartButton(false);
                }.bind(this),

                success: function (response) {
                    this.toggleAddToCartButton(true);
    
                    // for configurable products with dropdown attribute
                    $('.super-attribute-select').removeClass('super-attribute-select').addClass('super-attribute-select-tmp');

                    $cartContent.html(response.html);

                    $('body').trigger('contentUpdated');
    
                    // for configurable products with dropdown attribute
                    setTimeout(function () {
                        $('.super-attribute-select-tmp').removeClass('super-attribute-select-tmp').addClass('super-attribute-select');
                    }.bind(this), 500);
    
    
                    this.$items().each(function (_, el) {
                        const itemId = $(el).attr('data-item');
        
                        const mainBlock = $('.active' + this.productBlockSelector, el);
                        const productId = mainBlock.data('product-id');
        
                        let elId = '#cart_item_' + productId;
        
                        let elBlock = $(elId + '.mst-product_kit__cart-item');
        
                        if ($('.kit-item-oldPrice', mainBlock).length > 0) {
                            let discountPriceHtml = $(this.itemNewPriceSelector, mainBlock)[0].outerHTML;
                            $('.price-box', elBlock).addClass('kit-item-oldPrice').after(discountPriceHtml);
                        }
        
                    }.bind(this));

                    if (response.html) {
                        this.$el.addClass('_cart');
                    } else {
                        this.$el.removeClass('_cart');
                    }

                    if (response.success === true) {
                        if (this.options.action_name === 'checkout_cart_index') {
                            window.location.reload();
                        }
                        if (this.options.should_redirect) {
                            window.location = mageUrl.build('checkout/cart');
                        }
                    }

                }.bind(this),

                error: function (e) {
                    const $button = $(this.buttonSelector, this.$el);
                    $button.find('span').text($t('Unexpected error'));
                }.bind(this)
            });
        },

        isItemSelected: function (itemId) {
            const $checkbox = $('[data-element=checkbox]', this.$item(itemId));

            if ($checkbox.length === 0 || $checkbox.attr('checked') === 'checked') {
                return true
            }

            return false;
        },

        getAllItemIds: function () {
            const ids = [];

            this.$items().each(function (_, el) {
                ids.push($(el).attr('data-item'));
            });

            return ids;
        },

        getSelectedCombinationHash: function () {
            const ids = [];

            this.$items().each(function (_, el) {
                const itemId = $(el).attr('data-item');

                if (this.isItemSelected(itemId)) {
                    const id = itemId + '-' + $('.active' + this.productBlockSelector, el).data('product-id');

                    ids.push(id)
                }

            }.bind(this));

            return ids.join('/');
        },

        getSelectedCombinationQuoteHash: function () {
            const ids = [];

            this.$items().each(function (_, el) {
                const id = $(el).attr('data-item');

                if (this.isItemSelected(id)) {
                    ids.push(id)
                }

            }.bind(this));

            return ids.join('/');
        },

        $items: function () {
            return $('[data-item]', this.$el);
        },

        $item: function (itemId) {
            return $('[data-item=' + itemId + ']', this.$el);
        },

        toggleAddToCartButton: function (enabled) {
            const $button = $(this.buttonSelector, this.$el);

            if (enabled) {
                $button.find('span').text($t('Added'));

                setTimeout(function () {
                    this.$el.removeClass('_adding');
                    $button.removeClass('disabled');
                    $button.find('span').text($t('Add to Cart'));
                }.bind(this), 1000);
            } else {
                this.$el.addClass('_adding');
                $button.addClass('disabled');
                $button.find('span').text($t('Adding...'));
            }
        },

        changeProduct: function () {
            let productBlockSelector = '[data-element=product-block]';
            let productBlock         = $(this).parent(productBlockSelector);
            let itemBlock            = $(productBlock).parent();

            productBlock.removeClass('active');

            if ($(this).hasClass('up')) {
                if (productBlock.next().length) {
                    productBlock.next().addClass('active');
                } else {
                    $($(productBlockSelector, itemBlock)[0]).addClass('active');
                }
            } else {
                if (productBlock.prev().length) {
                    productBlock.prev().addClass('active');
                } else {
                    let lastIndex = $(productBlockSelector, itemBlock).length - 1;

                    $($(productBlockSelector, itemBlock)[lastIndex]).addClass('active');
                }
            }
        }
    });

    return $.mirasvit.kit;
});
