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

 define([
    "jquery",
    "Magento_Ui/js/modal/modal",
    "Magento_Catalog/product/view/validation",
    "domReady!"
], function ($, modal) {

        var pacModal = function () {
            var $this = this;

            $this.config = null;
            $this.asside = null;
            //Container for popup added with plugin
            $this.popup = {};

            $this.init = function(config) {
                $this.config = config;
                $this.popup = $(config.popupSelector);
                $this.configPopup();
                $this.modalOpen();
            }

            $this.configPopup = function() {
                $this.popup.modal({
                    modalClass : $this.config.modalClass,
                    buttons : [],
                    closed : $this.removeModalClass,
                });

                $(document).on("click", 'aside.pac-modal-popup', function(e) {
                    var isOpenPopup = $(this).hasClass('_show');

                    if (! document.querySelector('.modal-content:hover') && isOpenPopup) {
                        if (e.target && e.target.tagName == 'OPTION') {
                            return;
                        }
                        $this.popup.modal("closeModal");
                    }
                });
            }

            $this.modalOpen = function() {
                $this.popup.modal("openModal");
                //Add class for css
                $this.asside = $($this.config.asideSelector);
                $this.asside.addClass($this.config.fullActionName);
            }

            $this.removeModalClass = function() {
                //Remove class for css
                $this.asside.removeClass($this.config.fullActionName + ' pac-error-msg');
                $this.asside.find('.modal-inner-wrap')
                	.removeClass('popup-without-related');
            }
        }

    return new pacModal();
});
