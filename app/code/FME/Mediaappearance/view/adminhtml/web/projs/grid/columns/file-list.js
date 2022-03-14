define([
    './column',
    'jquery',
    'ko',
    'mage/template',
    'text!FME_Productattachments/template/preview.html',
    'Magento_Ui/js/modal/alert'
], function (Column, $, ko, mageTemplate, attachmentPreviewTemplate, alert) {
    'use strict';


    return Column.extend({
        defaults: {
            bodyTmpl: 'FME_Productattachments/file-list.html',
            fieldClass: {
                'data-grid-attachment-cell': true
            }
        },
        getAlt: function (row) {
            return row[this.index + '_alt']
        },
        getAttachmentList: function (row) {
            
            return row[this.index + '_list'];
        },

        getProductId: function (row) {
            return row['entity_id'];
        },

        getAttachmentListId: function (row) {
            return 'att-list-' + this.getProductId(row);
        },

        isPreviewAvailable: function () {
            return this.has_preview || false;
        },
        preview: function (row) {

            var attachmentList = this.getAttachmentList(row);
            var productId = this.getProductId(row);

            var modalHtml = mageTemplate(
                attachmentPreviewTemplate,
                {
                    attachmentList: attachmentList,
                    productId : productId
                }
            );

            var previewPopup = $('<div/>').html(modalHtml);
            previewPopup.modal({
                title: this.getAlt(row),
                innerScroll: true,
                buttons: [],
             }).trigger('openModal');
        },
        getFieldHandler: function (row) {
            if (this.isPreviewAvailable()) {
                return this.preview.bind(this, row);
            }
        },

        getMaxAttachmentItems: function () {

            return 4;
        }

    });
});
