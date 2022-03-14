define([
    'Magento_Ui/js/form/element/ui-select',
    'underscore'
], function (UiSelect, _) {
    return UiSelect.extend({
        defaults: {
            exports: {
                map: '${ $.provider }:data.map'
            }
        },

        initialize: function () {
            return this
                ._super();
        },

        initObservable: function () {
            return this._super()
                .observe([
                    'map'
                ]);
        },

        onUpdate: function (currentValue) {
            const map = {};

            _.each(currentValue, function (item) {
                map[item] = true;
            });

            this.map(map);

            return this._super();
        }
    })
});
