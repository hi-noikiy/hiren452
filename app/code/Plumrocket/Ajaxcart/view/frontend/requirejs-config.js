/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Ajaxcart v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

var config = {
    map: {
        "*": {
            "rightbar": "Plumrocket_Ajaxcart/js/prrightbar",
        }
    },
    'config': {
        'mixins': {
            'Magento_Checkout/js/view/minicart': {
                'Plumrocket_Ajaxcart/js/view/minicart-mixin': true
            },
            'Magento_Checkout/js/sidebar': {
                'Plumrocket_Ajaxcart/js/sidebar-mixin': true
            },
            'Magento_ConfigurableProduct/js/configurable': {
                'Plumrocket_Ajaxcart/js/configurable-mixin': true
            }
        }
    }
};
