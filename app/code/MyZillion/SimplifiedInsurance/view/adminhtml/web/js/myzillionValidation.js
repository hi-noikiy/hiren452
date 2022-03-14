require([
    'jquery',
    'mage/translate',
    'jquery/validate'],
    function($){
        $.validator.addMethod(
            'myzilion-product_type', function (v) {
                return (v != "0");
            }, $.mage.__('This field is required to complete the configuration.'));
    }
);
