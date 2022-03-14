define([
    'jquery',
    'underscore',
    'ko',
    'uiElement'
], function ($, _, ko, Element) {
    return Element.extend({
        lines: [],

        defaults: {
            template: 'Mirasvit_ProductKit/kit/form/evaluator',

            url: '',

            isLoading: false,

            imports: {
                data: '${ $.provider }:data'
            },

            listens: {
                data: 'onDataUpdate'
            }
        },

        initialize: function () {
            this._super();

            this.lazyUpdate = _.debounce(this.update.bind(this), 100);
        },

        initObservable: function () {
            this._super();

            this.lines = ko.observableArray(this.lines);
            this.isLoading = ko.observable(this.isLoading);

            return this;
        },

        onDataUpdate: function () {
            if (this.lazyUpdate) {
                this.lazyUpdate();
            }
        },

        update: function () {
            this.isLoading(true);

            $.ajax({
                url:     this.url,
                method:  'POST',
                data:    this.data,
                success: function (response) {
                    this.lines.removeAll();

                    _.each(response.items, function (item) {
                        this.lines.push(item)
                    }.bind(this))
                }.bind(this),

                complete: function () {
                    this.isLoading(false);
                }.bind(this)
            })
        }
    })
});
