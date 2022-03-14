define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/form/element/textarea'
], function ($, $t, Textarea) {
    'use strict';

    return Textarea.extend({

        defaults: {
            elementTmpl: 'Magezon_Builder/ui/form/element/builder',
            options: [],
            listVisible: false,
            showSpinner: false,
            showEmptyMessage: false,
            search: '',
            entityId: '',
            imports: {
                orientation: '${ $.provider }:data.pdf_orientation',
                pageSize: '${ $.provider }:data.pdf_page_size',
                marginTop: '${ $.provider }:data.pdf_margin_top',
                marginRight: '${ $.provider }:data.pdf_margin_right',
                marginBottom: '${ $.provider }:data.pdf_margin_bottom',
                marginLeft: '${ $.provider }:data.pdf_margin_left'
            },
            listens: {
                search: 'changeFormId',
                selectedType: 'changeType',
                '${ $.provider }:data.pdf_orientation': 'updateOrientation',
                '${ $.provider }:data.pdf_page_size': 'updatePageSize',
                '${ $.provider }:data.pdf_margin_top': 'updateMarginTop',
                '${ $.provider }:data.pdf_margin_right': 'updateMarginRight',
                '${ $.provider }:data.pdf_margin_bottom': 'updateMarginBottom',
                '${ $.provider }:data.pdf_margin_left': 'updateMarginLeft'
            },
            selectedType: 'product',
            types: [
                {
                    label: $t('Product'),
                    value: 'product'
                }
            ]
        },

        updateOrientation: function(orientation) {
            this.orientation(orientation);
            this.previewPdf();
        },

        updatePageSize: function(orientation) {
            this.pageSize(orientation);
            this.previewPdf();
        },

        updatePageNumber: function(orientation) {
            this.pageNumber(orientation);
            this.previewPdf();
        },

        updateMarginTop: function(orientation) {
            this.marginTop(orientation);
            this.previewPdf();
        },

        updateMarginRight: function(orientation) {
            this.marginRight(orientation);
            this.previewPdf();
        },

        updateMarginBottom: function(orientation) {
            this.marginBottom(orientation);
            this.previewPdf();
        },

        updateMarginLeft: function(orientation) {
            this.marginLeft(orientation);
            this.previewPdf();
        },

        initialize: function () {
            this._super();
            this.currentTab('builder');
            this.formKey(window.FORM_KEY);
            return this;
        },

        /**
         * @returns {*|void|Element}
         */
        initObservable: function () {
            return this._super().observe('currentTab formKey search options listVisible entityId selectedType showSpinner showEmptyMessage orientation pageSize pageNumber marginTop marginRight marginBottom marginLeft');
        },

        changeFormId: function(key) {

            // console.log(this.searchUrl);
            if (key) {
                var self = this;
                self.options([]);
                if (this.xhr) this.xhr.abort();
                self.showSpinner(true);
                this.xhr = $.ajax({
                    url: this.searchUrl,
                    type:'POST',
                    data: {
                        s: key,
                        type: self.selectedType()
                    },
                    success: function(res) {
                        if (res.status) {
                            if (res.options.length) {
                                self.showEmptyMessage(false);
                                self.listVisible(true);
                                self.options(res.options);
                            } else {
                                self.showEmptyMessage(true);
                                self.options([]);
                            }
                        }
                        self.showSpinner(false);
                    },
                    error: function(jqXHR,status,error) {

                    }
                });
            }
        },

        activeTab: function(type) {
            this.currentTab(type);
            if (type == 'preview') {
                this.previewPdf();
            }
        },

        outerClick: function () {
            this.listVisible() ? this.listVisible(false) : false;
        },

        toggleOptionSelected: function (item) {
            this.listVisible(false);
            this.entityId(item.value);
            this.search(item.label);
            this.previewPdf();
        },

        previewPdf: function() {
            if (this.entityId()) {
                $("#pdf-iframe").contents().find('html').html('<h1>' + $t('Loading....') + '</h1>');
                $('#pdf-form').trigger('submit');
            }
        },

        changeType: function(type) {
            this.entityId('');
            this.search('');
            $("#pdf-iframe").contents().find('html').html('');
        },

        onFormRender: function() {
            var self = this;
            $('#pdf-form').on('submit', function() {
                if (!self.entityId()) return false;
            });
        },
    })
});
