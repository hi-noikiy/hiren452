define([
    'Magento_Ui/js/form/element/abstract',
    'underscore'
], function (Text, _) {
    return Text.extend({
        defaults: {
            imports: {
                externalValue: '${ $.parentName }.${ $.listingName }:externalValue'
            },
            listens: {
                externalValue: 'onUpdateExternalValue'
            }
        },

        initialize: function () {
            this._super();
        },

        onUpdateExternalValue: function () {
            const values = _.map(this.externalValue, function (item) {
                return item['sku']
            });

            this.value(values.join(','));
        },
    })
});
