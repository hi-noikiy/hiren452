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

define([
    'MyZillion_SimplifiedInsurance/js/model/zillion',
    'jquery',
    'Amazon_Payment/js/model/storage',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/address-converter',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/checkout-data-resolver',
  ],
  function (
    zillion,
    $,
    amazonStorage,
    shippingService,
    addressConverter,
    storage,
    errorProcessor,
    urlBuilder,
    checkoutData,
    checkoutDataResolver,
  ) {
      'use strict';
      var mixin = {

          /**
           * Get shipping address from Amazon API
           */
          getShippingAddressFromAmazon: function () {
              var serviceUrl, payload;

              amazonStorage.isShippingMethodsLoading(true);
              shippingService.isLoading(true);
              serviceUrl = urlBuilder.createUrl('/amazon-shipping-address/:amazonOrderReference', {
                  amazonOrderReference: amazonStorage.getOrderReference()
              }),
                  payload = {
                      addressConsentToken: amazonStorage.getAddressConsentToken()
                  };

              storage.put(
                  serviceUrl,
                  JSON.stringify(payload)
              ).done(
                  function (data) {
                      var amazonAddress = data.shift(),
                          addressData = addressConverter.formAddressDataToQuoteAddress(amazonAddress),
                          i;

                      //if telephone is blank set it to 00000000 so it passes the required validation
                      addressData.telephone = !addressData.telephone ? '0000000000' : addressData.telephone;

                      //fill in blank street fields
                      if ($.isArray(addressData.street)) {
                          for (i = addressData.street.length; i <= 2; i++) {
                              addressData.street[i] = '';
                          }
                      }
                      checkoutData.setShippingAddressFromData(
                          addressConverter.quoteAddressToFormAddressData(addressData)
                      );
                      checkoutDataResolver.resolveEstimationAddress();

                      amazonStorage.isAmazonShippingAddressSelected(true);

                      zillion.checkQuoteOffer();
                  }
              ).fail(
                  function (response) {
                      errorProcessor.process(response);
                      //remove shipping loader and set shipping rates to 0 on a fail
                      shippingService.setShippingRates([]);
                      amazonStorage.isShippingMethodsLoading(false);
                      if (self.isShippingAddressReadOnly()) {
                          shippingService.isLoading(false);
                          $('.checkout-shipping-method').hide();
                      }
                  }
              );
          },
      };

      return function (target) { // target == Result that Magento_Ui/.../columns returns.
        return target.extend(mixin); // new result that all other modules receive
    };
  });
