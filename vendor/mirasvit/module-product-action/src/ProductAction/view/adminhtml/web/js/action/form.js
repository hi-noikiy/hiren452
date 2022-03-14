define([
    'Magento_Ui/js/form/form',
    'uiRegistry',
    'underscore'
], function (Form, Registry, _) {
    return Form.extend({
        defaults: {
            imports: {
                productIds: 'product_listing.product_listing.product_columns.ids',
                formData:   '${ $.provider }:data'
            },
            exports: {
                selection: '${ $.provider }:data.selection'
            }
        },

        initialize: function () {
            this._super();

            window.Registry = Registry;

            setInterval(function () {
                const idsComponent = Registry.get('product_listing.product_listing.product_columns.ids');
                if (!idsComponent) {
                    return;
                }
                const dataSource = Registry.get('product_listing.product_listing_data_source');

                const selectionsData = idsComponent.getSelections();
    
                if (selectionsData.excludeMode === true && selectionsData.excluded.length === 0) {
                    selectionsData.excluded.push('false');
                }
                
                const itemsType = selectionsData.excludeMode ? 'excluded' : 'selected';

                const selectionData = {};
                selectionData[itemsType] = selectionsData[itemsType];

                const selection = _.extend(selectionData, dataSource.params);
                this.selection(selection);
            }.bind(this), 100);
        },

        initObservable: function () {
            return this._super()
                .observe([
                    'selection'
                ]);
        }
    });
});
