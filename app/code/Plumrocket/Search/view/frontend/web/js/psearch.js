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
 * @package     Plumrocket Search Autocomplete & Suggest
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


define([
    "jquery",
    'jquery/ui',
    "domReady!"
], function ($) {
    "use strict";

    function Plumrocket_Search (config)
    {
        var $this = this;
        var $form = $('#pas-mini-form');
        var $tooltip = $('#pas-tooltip');
        var $overley = $('.pas-overley');

        this.config = {
            path:               '',
            delay:              500,
            queryLenghtMin:     2
        }
        $.extend(this.config, config);
        this.timeout = null;

        this.run = function()
        {
            // Left.
            $form.on('change', '.pas-nav-left .pas-search-dropdown', function() {
                var text = $.trim($(this).find('option:selected').text());
                $form.find('.pas-nav-left .pas-search-label').text(text);
            });

            // Center.
            $form.find('.pas-nav-center .pas-input-text').on('keyup', function() {
                var queryText = this.value;
                var categoryId = $form.find('.pas-nav-left .pas-search-dropdown').val();
                if(this.timeout) {
                    clearTimeout(this.timeout);
                }

                if(queryText.length >= $this.config.queryLenghtMin) {
                    this.timeout = setTimeout(function() {
                        $this._find(queryText, categoryId);
                    }, $this.config.delay);
                }else{
                    $this._hide();
                }
            }).on('blur', function() {
                setTimeout(function() {
                    $this._hide();
                }, 500);
            });
        }

        this._find = function(queryText, categoryId) {
            $form.find('.pas-loader').css('visibility', 'visible');

            $.post($this.config.path, {'q': queryText, 'cat': categoryId}, function(data) {
                data = JSON.parse($.trim(data));
                if (data.q == $form.find('.pas-nav-center .pas-input-text').val()) {
                    if(data.success && data.content) {
                        $tooltip.html(data.content);
                        $this._show();
                    }else{
                        $this._hide();
                    }
                }
            }).always(function() {
                $form.find('.pas-loader').css('visibility', 'hidden');
            }).fail(function() {});
        }

        this._show = function()
        {
            $form.addClass('pas-active');
            $overley.addClass('show');
        }

        this._hide = function()
        {
            $form.removeClass('pas-active');
            $overley.removeClass('show');
        }
    }

    return Plumrocket_Search;
});