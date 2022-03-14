<?php
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
 * @package   Plumrocket_AutoInvoiceShipment
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AutoInvoiceShipment\Block\Adminhtml\Shipmentrules\Edit;

use Magento\Backend\Block\Widget\Form\Generic as FormGeneric;

use Magento\CatalogRule\Model\Rule\CustomerGroupsOptionsProvider;
use Magento\CatalogRule\Model\Rule\WebsitesOptionsProvider;
use Plumrocket\AutoInvoiceShipment\Model\Shipmentrules;
use Plumrocket\AutoInvoiceShipment\Model\Config\Source\Status;
use Plumrocket\AutoInvoiceShipment\Model\Config\Source\CreateShipment;
use Plumrocket\AutoInvoiceShipment\Model\Config\Source\AppendCommentToEmail;

class Form extends FormGeneric
{
    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $rendererFieldset;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var CreateShipment
     */
    protected $createShipment;

    /**
     * @var WebsitesOptionsProvider
     */
    protected $websitesOptionsProvider;

    /**
     * @var CustomerGroupsOptionsProvider
     */
    protected $customerGroupsOptionsProvider;

    /**
     * @var AppendCommentToEmail
     */
    protected $appendCommentToEmail;

    /**
     * @param \Magento\Backend\Block\Template\Context              $context
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Framework\Data\FormFactory                  $formFactory
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param \Magento\Rule\Block\Conditions                       $conditions
     * @param WebsitesOptionsProvider                              $websitesOptionsProvider
     * @param CustomerGroupsOptionsProvider                        $customerGroupsOptionsProvider
     * @param Status                                               $status
     * @param CreateShipment                                       $createShipment
     * @param AppendCommentToEmail                                 $appendCommentToEmail
     * @param array                                                $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context                 $context,
        \Magento\Framework\Registry                             $registry,
        \Magento\Framework\Data\FormFactory                     $formFactory,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset    $rendererFieldset,
        \Magento\Rule\Block\Conditions                          $conditions,
        WebsitesOptionsProvider                                 $websitesOptionsProvider,
        CustomerGroupsOptionsProvider                           $customerGroupsOptionsProvider,
        Status                                                  $status,
        CreateShipment                                          $createShipment,
        AppendCommentToEmail                                    $appendCommentToEmail,
        array $data = []
    ) {
        $this->rendererFieldset                 = $rendererFieldset;
        $this->conditions                       = $conditions;
        $this->websitesOptionsProvider          = $websitesOptionsProvider;
        $this->customerGroupsOptionsProvider    = $customerGroupsOptionsProvider;
        $this->status                           = $status;
        $this->createShipment                   = $createShipment;
        $this->appendCommentToEmail             = $appendCommentToEmail;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return \Magento\Backend\Block\Widget\Form\Generic
     */
    protected function _prepareForm()
    {
        /**
         * @var Shipmentrules
         */
        $model = $this->_coreRegistry->registry('current_model');
        if (!$model->getId() && $model->getComment() === null) {
            $model->setComment(__(Shipmentrules::DEFAULT_COMMENT));
        }

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_authorization->isAllowed('Plumrocket_AutoInvoiceShipment::shipment_rules')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);
        $form->setHtmlIdPrefix('shipmentrules_');

        $fieldset = $form->addFieldset('base_fieldset', []);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name'      => 'name',
                'label'     => __('Rule Name'),
                'title'     => __('Rule Name'),
                'required'  => true,
                'disabled'  => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label'     => __('Status'),
                'title'     => __('Status'),
                'name'      => 'status',
                'required'  => true,
                'options'   => $this->status->toOptionArray(),
                'disabled'  => $isElementDisabled,
                'value'     => ($model->getStatus() !== null) ? $model->getStatus() : Shipmentrules::STATUS_ENABLED,
            ]
        );

        $fieldset->addField(
            'create_shipment',
            'select',
            [
                'label'     => __('Create Shipment'),
                'title'     => __('Create Shipment'),
                'name'      => 'create_shipment',
                'required'  => true,
                'options'   => $this->createShipment->toOptionArray(),
                'disabled'  => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'website',
            'multiselect',
            [
                'label'     => __('Website'),
                'title'     => __('Website'),
                'name'      => 'website[]',
                'required'  => true,
                'values'   => $this->websitesOptionsProvider->toOptionArray(),
                'disabled'  => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'customer_group',
            'multiselect',
            [
                'label'     => __('Customer Groups'),
                'title'     => __('Customer Groups'),
                'name'      => 'customer_group[]',
                'required'  => true,
                'values'   => $this->customerGroupsOptionsProvider->toOptionArray(),
                'disabled'  => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'comment',
            'textarea',
            [
                'label'     => __('Comment'),
                'title'     => __('Comment'),
                'name'      => 'comment',
                'required'  => false,
                'disabled'  => $isElementDisabled,
                'note'   => __('This comment will be displayed in Shipment History.'),
            ]
        );

        $fieldset->addField(
            'comment_to_email',
            'select',
            [
                'label'     => __('Append Comment To Shipment Email'),
                'title'     => __('Status'),
                'name'      => 'comment_to_email',
                'required'  => false,
                'options'   => $this->appendCommentToEmail->toOptionArray(),
                'disabled'  => $isElementDisabled,
                'value'     => ($model->getCommentToEmail() !== null)
                                ? $model->getCommentToEmail()
                                : Shipmentrules::APPEND_COMMENT_TO_EMAIL_NO,
            ]
        );

        $fieldset->addField(
            'rules_priority',
            'text',
            [
                'label'     => __('Rules Priority'),
                'title'     => __('Rules Priority'),
                'name'      => 'rules_priority',
                'disabled'  => $isElementDisabled,
                'note'      => __('Rule Priority is not required. The smaller the number - the higher is the priority. Rules with the highest priority will be used first.'),
            ]
        );

        /**
         * Conditions
         */
        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('*/*/newConditionHtml/form/shipmentrules_conditions_fieldset')
        );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            ['legend' => __('Apply the rule only to orders matching the following conditions (leave blank for all orders)')]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            [
                'name'      => 'conditions',
                'label'     => __('Conditions'),
                'title'     => __('Conditions'),
                'disabled'  => $isElementDisabled,
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->conditions
        );

        $this->_eventManager->dispatch(
            'plumrocket_shipmentrules_edit_tab_rules_prepare_form',
            ['form' => $form]
        );

        $data = $model->getData();
        if (!isset($data['status'])) {
            $data['status'] = 1;
        }

        if (isset($data['websites']) && !is_array($data['websites'])) {
            $data['website'] = explode(',', $data['websites']);
        }

        if (isset($data['customer_groups']) && !is_array($data['customer_groups'])) {
            $data['customer_group'] = explode(',', $data['customer_groups']);
        }

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Check permission for passed action
     *
     * @param  string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
