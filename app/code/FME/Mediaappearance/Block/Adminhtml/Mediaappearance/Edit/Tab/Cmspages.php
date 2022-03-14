<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Mediaappearance
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Mediaappearance\Block\Adminhtml\Mediaappearance\Edit\Tab;

class Cmspages extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    
    /**
     *
     * @param \Magento\Backend\Block\Template\Context    $context
     * @param \Magento\Framework\ObjectManagerInterface  $objectManager
     * @param \FME\Mediaappearance\Model\Mediaappearance $model
     * @param \Magento\Customer\Model\Group              $customer_group
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Framework\Data\FormFactory        $formFactory
     * @param \Magento\Store\Model\System\Store          $systemStore
     * @param array                                      $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \FME\Mediaappearance\Model\Mediaappearance $model,
        \Magento\Customer\Model\Group $customer_group,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_objectManager = $objectManager;
        $this->_model = $model;
        $this->_customergroup = $customer_group;
        parent::__construct($context, $registry, $formFactory, $data);
    }

   
    /**
     * _prepareForm
     * @return extended
     */
    protected function _prepareForm()
    {
        
        $form = $this->_formFactory->create();
        $this->setForm($form);
        $fieldset = $form->addFieldset('mediaappearance_form', ['legend'=>__('Attach With CMS Pages')]);
      
        $fieldset->addField('cmspage_id', 'multiselect', [
            'name'      => 'cmspage_id',
            'label'     => __('CMS Pages'),
            'title'     => __('CMS Pages'),
            'required'  => false,
            'style'     =>  'width:200px',
            'values'    => $this->_model->getCMSPage()
        ]);
    
        $data = $this->_coreRegistry->registry('mediagallery_data');
        $form->setValues($data);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('CMS Page');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('CMS Page');
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

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
