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
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * Types
     * @var \Plumrocket\Affiliate\Model\ResourceModel\Type\Collection
     */
    protected $_types;

    /**
     * Current Type
     * @var \Plumrocket\Affiliate\Model\Type
     */
    protected $_currentType;

    /**
     * Affiliate Type factory
     * @var \Plumrocket\Affiliate\Model\TypeFactory
     */
    protected $_affiliateTypeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Plumrocket\Affiliate\Model\TypeFactory $affiliateTypeFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Plumrocket\Affiliate\Model\TypeFactory $affiliateTypeFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_affiliateTypeFactory = $affiliateTypeFactory;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        $form = $this->_formFactory->create();

        $model = $this->getAffiliate();
        $type = $this->getType();

        $form->setHtmlIdPrefix('affiliate_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Add New Affiliate Program')]);

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_authorization->isAllowed('Plumrocket_Affiliate::affiliate_manage')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $isElementDisabled = false;

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                [
                    'name'      => 'id',
                    'value'     => $model->getId() ?: null,
                ]
            );
        }

        $fieldset->addField(
            'type_id',
            'hidden',
            [
                'name'      => 'type_id',
                'value'     => $model->getTypeId() ?: $type->getId(),
            ]
        );

        $fieldset->addField(
            'network',
            'note',
            [
                'name' => 'network',
                'label' => __('Affiliate Network'),
                'title' => __('Affiliate Network'),
                'text' => $this->getNetworkLabel($model)
            ]
        );


        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'value' => $model->getData('name') ?: $type->getData('name'),
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => \Plumrocket\Affiliate\Model\Affiliate::getAvailableStatuses(),
                'disabled' => $isElementDisabled,
                'value' => ($model->getStatus() !== null) ? $model->getStatus() : 1,
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {

            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled,
                    'value'     => $model->getStores()
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }


        $this->_eventManager->dispatch('plumrocket_affiliate_edit_tab_main_prepare_form', ['form' => $form]);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get label for affiliate network
     * @param  Plumrocket\Affiliate\Model\Type $model
     * @return string        image or text (for custom mode)
     */
    public function getNetworkLabel($model)
    {
        $key = $this->getType()->getKey();
        if ($key === 'tradedoubler') {
            return '<img src="' . $this->getViewFileUrl('Plumrocket_Affiliate::images/' . strtolower($key) . 'logo.png') . '" />';
        }

        if ($key === 'custom') {
            return __('Custom');
        }

        return '<img src="' . $this->getViewFileUrl('Plumrocket_Affiliate::images/' . strtolower($key) . '.png') . '" />';
    }

    /**
     * get current model
     */
    public function getAffiliate()
    {
        return $this->_coreRegistry->registry('current_model');
    }

    /**
     * Get type
     * @return Plumrocket\Affiliate\Model\Type
     */
    public function getType()
    {
        $typeId = $this->getAffiliate()->getTypeId() ?: $this->getRequest()->getParam('type_id');
        if ($this->_currentType === null) {
            $this->_currentType = $this->getTypes()
                ->addFieldToFilter('id', $typeId)
                ->setPageSize(1)
                ->getFirstItem();
        }
        return $this->_currentType;
    }

    /**
     * get all types
     * @return Plumrocket\Affiliate\Model\Type
     */
    public function getTypes()
    {
        if ($this->_types === null) {
            $this->_types = $this->_affiliateTypeFactory->create()->getCollection();
        }
        return $this->_types;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
