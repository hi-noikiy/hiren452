var config = {
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'JB_DynamicAttribute/js/model/attswitch': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'JB_DynamicAttribute/js/model/swatch-attswitch': true
            }
        }
    }
};