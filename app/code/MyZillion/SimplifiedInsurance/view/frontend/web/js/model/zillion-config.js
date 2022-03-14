/**
 * Copyright (c) 2020, Zillion Insurance Services, Inc.
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @author Exequiel Lares <exequiel@serfe.com>
 */

/**
 * @api
 */
define([
  'underscore'
], function (_) {
    'use strict';

    var binderTemplate = 'MyZillion_SimplifiedInsurance/ui/form/element/binder-offer-template';
    var quoteTemplate = 'MyZillion_SimplifiedInsurance/ui/form/element/quote-offer-template';
    return _.extend({
        /**
         * Retrieve Quote Offer template path
         * @return {String} [description]
         */
        getBinderTemplate: function() {
            return binderTemplate;
        },
        /**
         * Retrieve Quote Offer template path
         * @return {String} [description]
         */
        getQuoteTemplate: function() {
            return quoteTemplate;
        },
        /**
         * Check if zillion box is enabled
         * @return {Boolean} [description]
         */
        isModuleEnabled: function() {
            if (window.checkoutConfig.zillionConfig && window.checkoutConfig.zillionConfig.is_enabled) {
                return true;
            }
            return false;
        },
        /**
         * Retrieve template to use based on module configuration
         * @return {String} [description]
         */
        getZillionBoxTemplate: function() {
            // Default value is binder
            let type = 'binder';
            if (window.checkoutConfig.zillionConfig && window.checkoutConfig.zillionConfig.offer_type) {
                type = window.checkoutConfig.zillionConfig.offer_type;
            }
            if (type == 'quote') {
                return this.getQuoteTemplate();
            } else {
                return this.getBinderTemplate();
            }
        }
    })
});
