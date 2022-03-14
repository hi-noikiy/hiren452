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
            bodyTmpl: 'FME_Productattachments/cms/file-list.html',
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

        getCmsId: function (row) {
            return row['page_id'];
        },

        getAttachmentListId: function (row) {
            return 'att-list-' + this.getCmsId(row);
        },

        isPreviewAvailable: function () {
            return this.has_preview || false;
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
