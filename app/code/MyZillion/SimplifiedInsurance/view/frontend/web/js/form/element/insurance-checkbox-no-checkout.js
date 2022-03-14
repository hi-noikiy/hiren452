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
 * @author Guillermo Holmann <guille@serfe.com>.
 */

define([
  'jquery',
  'underscore',
  'ko',
  'Magento_Ui/js/modal/modal',
  'Magento_Ui/js/form/element/boolean',
  'mage/url',
  'Magento_Customer/js/customer-data',
  'mage/storage',
  'Magento_Ui/js/modal/confirm',
  'MyZillion_SimplifiedInsurance/js/model/zillion',
  'MyZillion_SimplifiedInsurance/js/model/zillion-config',
  'MyZillion_SimplifiedInsurance/js/action/request-insurance-no-checkout',
  'loader'
], function (
  $,
  _,
  ko,
  modal,
  Component,
  urlBuilder,
  customerData,
  storage,
  confirmation,
  zillion,
  zillionConfig,
  requestInsuranceActionNoCheckout
) {
  "use strict";

  var loader = $('#zillion-loader').loader({
      icon: 'icon.gif'
  });

  return Component.extend({
    //set the checkbox to false
    checkVal: ko.observable(false),

    isVisible: ko.observable(),

    // Binder amount from getOffer. Is ussed in the binder template
    binderAmount: ko.observable(0),

    initialize: function () {
      var self = this;
      this._super();
      this.template = zillionConfig.getZillionBoxTemplate();
      loader.loader('show');
      var self = this;
    },

    initObservable: function () {
      this._super();

      var self = this;
      if (this.getCheckboxCheckedValue() == 1) {
        self.checkVal(true);
        zillion.have_insurance(true);
      }
      // Update customer request insurance value in quote
      requestInsuranceActionNoCheckout({
          customer_request_insurance: this.getCheckboxCheckedValue()
      });

      zillion.isBoxVisible.subscribe(function (val) {
          self.isVisible(val);
          if (val) {
            self.setShowBoxValue(1);
          } else {
            self.setShowBoxValue(0);
          }
      })

      zillion.offerValue.subscribe(function (val) {
        self.binderAmount(val);
      })

      zillion.checkQuoteOffer(function(res) {
        loader.loader('hide');
        if (self.getShowBoxValue() == 1) {
          self.isVisible(true);
        }
        if (res.is_insurable && res.offer_value) {
            self.binderAmount(res.offer_value)
        }
      });

      this.checkVal.subscribe(function (newValue) {
        //  display modal is the checkbox is checked
        if (newValue) {
          confirmation({
            title: $.mage.__("I agree that in the last 3 years I have NOT had:"),
            content: $.mage.__("<ul><li>More than one jewelry loss paid by an insurance company</li><li>Jewelry coverage declined, canceled or non-renewed</li><li>A conviction (other than a traffic misdemeanor)</li></ul>"),
            modalClass: 'myzillion__modal-add-insurance',
             actions: {
              confirm: function () {
                self.setCheckboxCheckedValue(1);
                //set observable value to true
                zillion.have_insurance(true);
              },
              cancel: function () {
                //set unchecked box
                self.checkVal(false);
                self.setCheckboxCheckedValue(0);
                //set observable value to false
                zillion.have_insurance(false);
              },
            },
            buttons: [
              {
                text: $.mage.__("I AGREE"),
                class: "action primary action-accept zillion",
                click: function (event) {
                  this.closeModal(event, true);
                },
              },
              {
                text: $.mage.__("Cancel"),
                class: "action-secondary action-dismiss",
                click: function (event) {
                  this.closeModal(event);
                },
              },
            ],
          });
        } else {
          //set unchecked box
          self.checkVal(false);
          self.setCheckboxCheckedValue(0);
          zillion.have_insurance(false);
        }
      });

      return this;
    },

    setCheckboxCheckedValue: function (value) {
      var zillionData = customerData.get('myzillion-data')()
      if (_.isEmpty(zillionData)) {
         zillionData = {
           checkbox_checked: value
         }
      } else {
          zillionData.checkbox_checked = value;
      }
      requestInsuranceActionNoCheckout({
          customer_request_insurance: value
      });
      customerData.set('myzillion-data', zillionData);
    },

    getCheckboxCheckedValue: function () {
        var zillionData = customerData.get('myzillion-data')()
        if (_.isEmpty(zillionData) || !zillionData.hasOwnProperty('checkbox_checked')) {
            return 0;
        }
        return zillionData.checkbox_checked;
    },

    setShowBoxValue: function (value) {
      var zillionData = customerData.get('myzillion-data')()
      if (_.isEmpty(zillionData)) {
         zillionData = {
           show_box: value
         }
      } else {
          zillionData.show_box = value;
      }

      customerData.set('myzillion-data', zillionData);
    },

    getShowBoxValue: function () {
        var zillionData = customerData.get('myzillion-data')()
        if (_.isEmpty(zillionData) || !zillionData.hasOwnProperty('show_box')) {
            return 0;
        }
        return zillionData.show_box;
    }
  });
});
