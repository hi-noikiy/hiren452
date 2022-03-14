define([
    'jquery',
    'uiRegistry'
], function ($, registry) {
    'use strict';
    
    var mixin = {
    
        toggleOptionSelected: function (data) {
            let result = this._super(data);
            
            if (this.name == "category_form.category_form.assign_products.mst_rule_loader") {
                let component = registry.get('mstDynamicCategoryProductsPreview');
                
                let loaderUrl = component.loaderUrl;
                
                $.ajax({
                    url:      loaderUrl,
                    type:     'POST',
                    dataType: 'html',
                    data:     {category_id: this.value(), form_key: window.FORM_KEY},
        
                    beforeSend: function () {
                        $('body').trigger('processStart');
                    }.bind(this),
        
                    success: function (response) {
                        if (response) {
                            $('.mst-rule-conditions').html(response)
                                .trigger('contentUpdated');
                
                            this.value([]);
                        }
    
                        $('body').trigger('processStop');
                    }.bind(this),
        
                    error: function (response) {
                        $('body').trigger('processStop');
                    }.bind(this)
                });
            }
            
            return result;
        }
    };
    
    return function (target) { // target == Result that Magento_Ui/.../columns returns.
        return target.extend(mixin); // new result that all other modules receive
    };
});
