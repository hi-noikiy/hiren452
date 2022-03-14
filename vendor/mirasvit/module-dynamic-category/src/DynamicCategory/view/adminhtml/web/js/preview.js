define([
    'underscore',
    'ko',
    'uiComponent',
    'jquery',
    'uiRegistry'
], function (_, ko, Component, $, registry) {
    let requestUrl = "";
    let isGridReady = false;

    return Component.extend({
        default: {
            previewUrl: ''
        },

        initialize: function () {
            this._super();

            requestUrl = this.previewUrl;

            $('[data-dynamic-category = refresh]').on('click', function () {
                let categoryForm = registry.get('category_form.category_form');
                categoryForm.additionalFields = document.querySelectorAll(categoryForm.selector);

                let additional = collectData(categoryForm.additionalFields),
                    source     = categoryForm.source;

                _.each(source.data, function (value, name) {
                    if (name.indexOf('rule[conditions]') >= 0) {
                        source.remove('data.' + name, value);
                    }
                });

                _.each(additional, function (value, name) {
                    source.set('data.' + name, value);
                });

                sendRequest(source.data);
            });

            function sendRequest(data) {
                const grid = window['catalog_category_productsJsObject'];

                grid.url = requestUrl;
                grid.reloadParams = data;

                grid.reload();
            }

            /**
             * Copied from Magento_Ui/js/form/form.js.
             *
             * @param {Array} items
             * @returns {Object}
             */
            function collectData(items) {
                var result = {},
                    name;

                items = Array.prototype.slice.call(items);

                items.forEach(function (item) {
                    switch (item.type) {
                        case 'checkbox':
                            result[item.name] = +!!item.checked;
                            break;

                        case 'radio':
                            if (item.checked) {
                                result[item.name] = item.value;
                            }
                            break;

                        case 'select-multiple':
                            name = item.name.substring(0, item.name.length - 2); //remove [] from the name ending
                            result[name] = _.pluck(item.selectedOptions, 'value');
                            break;

                        default:
                            result[item.name] = item.value;
                    }
                });

                return result;
            }

            return this;
        }
    });
});
