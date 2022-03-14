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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

require([
    'jquery',
    'mage/translate',
    'domReady!'
], function($) {
    'use strict';

    var modeElement = $('#prajaxcart_additional_configuration_mode');
    changeComment(); /* run after page load */

    $(modeElement).on('change', function() {
        changeComment();
    });

    function changeComment()
    {
        if (modeElement.length) {
            if (modeElement.val() === '0') {
                modeElement.next().text($.mage.__('Automatically find and apply Ajax Cart functionality to all "Add to Cart" buttons.'));
            } else if (modeElement.val() === '1') {
                modeElement.next().text($.mage.__('Manually apply Ajax Cart functionality to all "Add to Cart" buttons with matching HTML class selector.'));
            }
        }
    }
});