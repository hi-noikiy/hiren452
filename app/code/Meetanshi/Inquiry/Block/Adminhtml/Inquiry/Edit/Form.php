<?php

namespace Meetanshi\Inquiry\Block\Adminhtml\Inquiry\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Store\Model\System\Store;
use Meetanshi\Inquiry\Model\Config\Source\Status;

class Form extends Generic
{
    protected $systemStore;
    protected $logger;
    protected $options;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Store $store,
        Status $options,
        array $data = []
    )
    {

        $this->_wysiwygConfig = $wysiwygConfig;
        $this->systemStore = $store;
        $this->options = $options;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('row_data');

        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form',
                'enctype' => 'multipart/form-data',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]
            ]
        );

        $form->setHtmlIdPrefix('wkgrid_');

        if ($model->getId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Dealer Information'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('dealer_id', 'hidden', ['name' => 'dealer_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Dealer Details'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'first_name',
            'text',
            [
                'name' => 'first_name',
                'label' => __('First Name'),
                'id' => 'first_name',
                'title' => __('First Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'last_name',
            'text',
            [
                'name' => 'last_name',
                'label' => __('Last Name'),
                'id' => 'last_name',
                'title' => __('Last Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email ID'),
                'id' => 'email',
                'title' => __('Email ID'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'company_name',
            'text',
            [
                'name' => 'company_name',
                'label' => __('Company Name'),
                'id' => 'company_name',
                'title' => __('Company Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'tax_vat_number',
            'text',
            [
                'name' => 'tax_vat_number',
                'label' => __('Tax/VAT Number'),
                'id' => 'company_name',
                'title' => __('Tax/VAT Number'),
            ]
        );

        $fieldset->addField(
            'address',
            'text',
            [
                'name' => 'address',
                'label' => __('Address'),
                'id' => 'address',
                'title' => __('Address'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'id' => 'city',
                'title' => __('City'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'zip_postal_code',
            'text',
            [
                'name' => 'zip_postal_code',
                'label' => __('Postal Code'),
                'id' => 'zip_postal_code',
                'title' => __('Postal Code'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'country',
            'text',
            [
                'name' => 'country',
                'label' => __('Country'),
                'id' => 'country',
                'title' => __('Country'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'state',
            'text',
            [
                'name' => 'state',
                'label' => __('State'),
                'id' => 'state',
                'title' => __('State'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'contact_number',
            'text',
            [
                'name' => 'contact_number',
                'label' => __('Contact No'),
                'id' => 'contact_number',
                'title' => __('Contact No'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'business_description',
            'textarea',
            [
                'name' => 'business_description',
                'label' => __('Business Description'),
                'id' => 'business_description',
                'title' => __('Business Description'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'files',
            'image',
            [
                'title' => __('Select Image'),
                'label' => __('Select Image'),
                'name' => 'files',
                'required' => false,
                'note' => 'Allow image type: jpg, jpeg, gif, png',
            ]
        );

        $fieldset->addType(
            'image_render',
            '\Meetanshi\Inquiry\Block\Adminhtml\Inquiry\Edit\Renderer\Imagerender'
        );

        $fieldset->addField(
            'image',
            'image_render',
            [
                'name' => 'image',
                'label' => __('Image(s)'),
                'title' => __('Image(s)'),

            ]
        );
        $fieldset->addField(
            'extra_field_1',
            'text',
            [
                'name' => 'extra_field_1',
                'label' => __('Extra Field 1'),
                'id' => 'extra_field_1',
                'title' => __('Extra Field 1'),
            ]
        );
        $fieldset->addField(
            'extra_field_2',
            'text',
            [
                'name' => 'extra_field_2',
                'label' => __('Extra Field 2'),
                'id' => 'extra_field_2',
                'title' => __('Extra Field 2'),
            ]
        );

        $fieldset->addField(
            'extra_field_3',
            'text',
            [
                'name' => 'extra_field_3',
                'label' => __('Extra Field 3'),
                'id' => 'extra_field_3',
                'title' => __('Extra Field 3'),
            ]
        );

        $fieldset->addField(
            'store_view',
            'multiselect',
            [
                'name' => 'store_view[]',
                'label' => __('Store View'),
                'id' => 'store_view',
                'title' => __('Store View'),
                'class' => 'required-entry',
                'values' => $this->systemStore->getStoreValuesForForm(false, true),
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
