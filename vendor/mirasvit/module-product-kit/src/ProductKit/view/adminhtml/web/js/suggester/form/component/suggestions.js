define([
    'jquery',
    'underscore',
    'ko',
    'uiElement'
], function ($, _, ko, Element) {
    return Element.extend({
        message: '',
        suggestions: [],

        defaults: {
            template: 'Mirasvit_ProductKit/suggester/form/suggestions',

            url: '',

            isLoading: false,

            imports: {
                data: '${ $.provider }:data'
            },
        },

        initialize: function () {
            this._super();

            $("body").on( "click", '.create-suggestion', this.createSuggestion.bind(this));
            $("body").on( "click", '.generate-suggestions-button', this.generateSuggestions.bind(this));
        },

        initObservable: function () {
            this._super();

            this.message     = ko.observable(this.message);
            this.suggestions = ko.observableArray(this.suggestions);
            this.isLoading   = ko.observable(this.isLoading);

            return this;
        },

        generateSuggestions: function () {
            this.isLoading(true);

            this.suggestions.removeAll();

            $.ajax({
                url:     this.url,
                method:  'POST',
                data:    this.data,
                success: function (response) {
                    _.each(response.items, function (item) {
                        this.suggestions.push(item)
                    }.bind(this));

                    this.message(response.message);
                }.bind(this),

                complete: function () {
                    this.isLoading(false);
                }.bind(this)
            })
        },

        createSuggestion: function (event) {
            var self = this;
            var suggestionData = {'suggestion': []};

            var suggestion = $(event.target).parents('.suggestion')[0];

            if ($(suggestion).length) {
                _.each($('.items .sku', $(suggestion)), function (skuEl) {
                    suggestionData.suggestion.push($(skuEl).html())
                });
            }

            this.isLoading(true);

            $.ajax({
                url:     this.createUrl,
                method:  'POST',
                data:    suggestionData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        window.open(response.kit_url, '_blank');
                    } else {
                        $('#messages .messages').append(
                            '<div class="message message-error error"><div data-ui-id="messages-message-error">' + response.error + '</div></div>'
                        );
                    }
                }.bind(this),

                complete: function () {
                    this.isLoading(false);
                }.bind(this)
            })
        }
    })
});
