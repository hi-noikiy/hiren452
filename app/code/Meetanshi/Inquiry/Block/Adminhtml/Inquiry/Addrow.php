<?php

namespace Meetanshi\Inquiry\Block\Adminhtml\Inquiry;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Addrow extends Container
{
    protected $coreRegistry = null;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    )
    {

        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'row_id';
        $this->_blockGroup = 'Meetanshi_Inquiry';
        $this->_controller = 'adminhtml_inquiry';
        $id = $this->getRequest()->getParam('id');
        parent::_construct();
        $url = $this->getUrl('meetanshi_inquiry/inquiry/delete', ['id' => $id]);
        if ($this->_isAllowedAction('Meetanshi_Inquiry::inquiry')) {
            $this->buttonList->update('save', 'label', __('Save'));
        } else {
            $this->buttonList->remove('save');
        }

        if (!empty($id)) {
            $this->buttonList->add(
                'delete',
                [
                    'label' => __('Delete Dealer'),
                    'class' => 'save',
                    'onclick' => 'setLocation(\'' . $url . '\')'
                ],
                -100
            );
            $createCustomerUrl = $this->getUrl('meetanshi_inquiry/inquiry/createcustomer', ['id' => $id]);
            $this->buttonList->add(
                'create',
                [
                    'label' => __('Create Customer'),
                    'class' => 'save',
                    'onclick' => 'setLocation(\'' . $createCustomerUrl . '\')'
                ],
                -100
            );
        }
        $this->buttonList->remove('reset');
    }

    public function getHeaderText()
    {
        return __('Add Dealer Inquiry');
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl('*/*/save');
    }
}
