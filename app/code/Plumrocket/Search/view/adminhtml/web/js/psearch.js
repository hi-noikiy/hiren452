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

require([
    "jquery",
    'jquery/ui',
    "domReady!"
], function ($) {
    "use strict";

    // Sortable Attributes.
    $('ul#sortable-attributes, ul#sortable-searchable').sortable({
        connectWith: "ul",

        receive: function(event, ui) {
            var id = ($(this).data('list') +'-'+ $(ui.item).data('id'));
            ui.item.attr('id', id);
        },

        update: function(event, ui) {
            if(event.target.id == 'sortable-searchable') {
                $('#sortable-searchable li').each(function(i) {
                    var $this = $(this);
                    $this.attr('id', $this.parent().data('list') + '-' + $this.data('id'));
                    $this.text((i + 1) + ' | ' + $this.data('name'));
                });
            } else if(event.target.id == 'sortable-attributes') {
                $('#sortable-attributes li').each(function() {
                    var $this = $(this);
                    $this.attr('id', $this.parent().data('list') + '-' + $this.data('id'));
                    $this.text($this.data('name'));
                });
            }

            var sortableAttributes = [];
            sortableAttributes.push("0");
            $('#sortable-searchable > li').each(function() {
                var liElement = $(this);
                sortableAttributes.push(liElement.attr('data-id'));
            });

            $('#psearch-attributes-change').val(JSON.stringify(sortableAttributes));
        }
    }).disableSelection();
});