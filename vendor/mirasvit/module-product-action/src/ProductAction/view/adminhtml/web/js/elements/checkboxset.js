define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/checkbox-set'
], function ($, _, registry, UiCheckboxSet) {
    return UiCheckboxSet.extend({
        onUpdate: function (currentValue) {
            var result = this._super();
            
            _.each(this.options, function (item) {
                var component = registry.get(this.parentName + '.mass_update_attributes_values.attribute_' + item.value);
                if (component) {
                    component.hide();
                }
            }.bind(this));
            
            _.each(currentValue, function (item) {
                var component = registry.get(this.parentName + '.mass_update_attributes_values.attribute_' + item);
                if (component) {
                    component.show();
                }
            }.bind(this));
            
            return result;
        }
    })
});
