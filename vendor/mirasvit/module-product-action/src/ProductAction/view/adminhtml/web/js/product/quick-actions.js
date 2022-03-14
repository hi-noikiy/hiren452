define([
    'jquery',
    'underscore',
    'uiRegistry',
    'uiElement'
], function ($, _, registry, UiElement) {
    return UiElement.extend({
        intervalId: null,
        
        defaults: {
            mstMassActions: [],
            visibility: false
        },
        
        initObservable: function () {
            this._super();
            
            this.observe('mstMassActions');
            this.observe('visibility');
    
            this.intervalId = setInterval(function () {
                this.initActions();
            }.bind(this), 300);

            return this;
        },
    
        mstApplyAction: function (actionIndex) {
            if (registry.get("product_listing.product_listing.listing_top.listing_massaction")) {
                registry.get("product_listing.product_listing.listing_top.listing_massaction").applyAction(actionIndex);
            }
            
            return this;
        },
    
        initActions: function () {
            let actions = [];
    
            if (registry.get("product_listing.product_listing.listing_top.listing_massaction")) {
                _.each(registry.get("product_listing.product_listing.listing_top.listing_massaction").actions(), function (action) {
                    if (typeof action.callback != 'undefined' &&
                        typeof action.callback[0]['targetName'] != 'undefined' &&
                        action.callback[0]['targetName'].indexOf('mst_product_action_action') >= 0
                    ) {
                        actions.push(action);
                    }
                }.bind(this));
    
                this.mstMassActions(actions);
    
                clearInterval(this.intervalId);
            }
        }
    })
});
