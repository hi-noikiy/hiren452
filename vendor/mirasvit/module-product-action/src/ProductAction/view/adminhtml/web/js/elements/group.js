define([
    'Magento_Ui/js/lib/core/collection',
    'underscore'
], function (Collection, _) {
    return Collection.extend({
        defaults: {
            visible: true
        },

        initialize: function () {
            return this
                ._super();
        },

        initObservable: function () {
            return this._super()
                .observe([
                    'visible'
                ]);
        }

    })
});
