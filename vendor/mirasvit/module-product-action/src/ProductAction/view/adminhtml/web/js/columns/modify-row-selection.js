define([
    'uiRegistry'
], function (registry) {
    'use strict';

    var mixin = {
        onSelectedChange: function (selected) {
            let result = this._super(selected);

            let quickActions = registry.get('product_listing.product_listing.mst_product_action_quick_actions_block');

            if (quickActions) {
                if (this.totalSelected() > 0) {
                    quickActions.visibility(true);
                } else {
                    quickActions.visibility(false);
                }
            }

            return result;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
