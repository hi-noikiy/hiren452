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

define([
    'jquery',
    'jquery/ui',
    'mage/translate'
], function ($) {
    'use strict';

    var timer = null;

    $.widget('prajaxcart.rightbar', {
        options: {
            triggerEvent: 'click',
            triggerClass: null,
            parentClass: null,
            triggerTarget: null,
            dialogContentClass: null,
            closeOnMouseLeave: true,
            closeOnClickOutside: true,
            timeout: null,
            draggable: false,
            resizable: false,
            bodyClass: '',
            buttons: [
                {
                    'class': 'action close',
                    'text': $.mage.__('Close'),

                    /**
                     * Click action.
                     */
                    'click': function () {
                        $(this.options.appendTo).find('.rightbar').toggle('slide', {direction: 'right'});
                    }
                }
            ]
        },

        /**
         * @private
         */
        _create: function () {
            var _self = this;

            this._super();

            if (_self.options.triggerTarget) {
                $(_self.options.triggerTarget).on(_self.options.triggerEvent, function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    if (!_self._isOpen()) {
                        $('.' + _self.options.defaultDialogClass + ' > .ui-dialog-content').rightbar('close');
                        _self.open();
                    } else {
                        _self.close(event);
                    }
                });
            }

            if (_self.options.shadowHinter) {
                _self.hinter = $('<div class="' + _self.options.shadowHinter + '"/>');
                _self.element.append(_self.hinter);
            }
        },

        open: function () {
            var _self = this;
            var rightbarBlock = $(this.options.appendTo).find('.rightbar');
            rightbarBlock.addClass('active');

            if (_self.options.dialogContentClass) {
                _self.element.addClass(_self.options.dialogContentClass);
            }

            if (_self.options.closeOnMouseLeave) {

                this._mouseEnter(_self.uiDialog);
                this._mouseLeave(_self.uiDialog);

                if (_self.options.triggerTarget) {
                    this._mouseLeave($(_self.options.triggerTarget));
                }
            }

            if (_self.options.closeOnClickOutside) {
                $('body').click(function(event) {
                   let container = $('.rightbar');
                   if(!container.is(event.target) && container.has(event.target).length === 0) {
                       if (timer) {
                            clearTimeout(timer);
                        }
                       _self.close(event);
                   }
                });
            }
        },

        close: function () {

            var rightbarBlock = $(this.options.appendTo).find('.rightbar');
            rightbarBlock.removeClass('active');

            if (this.options.dialogContentClass) {
                this.element.removeClass(this.options.dialogContentClass);
            }

            if (this.options.triggerClass) {
                $(this.options.triggerTarget).removeClass(this.options.triggerClass);
            }

            if (this.options.parentClass) {
                $(this.options.appendTo).removeClass(this.options.parentClass);
            }

            if (this.options.bodyClass) {
                $('body').removeClass(this.options.bodyClass);
            }

            if (timer) {
                clearTimeout(timer);
            }

            if (this.options.triggerTarget) {
                $(this.options.triggerTarget).off('mouseleave');
            }

            $('body').off('click');
        },

        _isOpen: function() {
            return $(this.options.appendTo).find('.rightbar').hasClass('active');
        },

         _mouseLeave: function (handler) {
            var _self = this;

            handler.on('mouseleave', function (event) {
                event.stopPropagation();

                if (_self._isOpen()) {
                    if (timer) {
                        clearTimeout(timer);
                    }
                    timer = setTimeout(function (e) {
                        _self.close(e);
                    }, _self.options.timeout);
                }
            });
        },

        _mouseEnter: function (handler) {
            handler.on('mouseenter', function (event) {
                event.stopPropagation();

                if (timer) {
                    clearTimeout(timer);
                }
            });
        },

    });

    return $.prajaxcart.rightbar;
});
