<?php

namespace Meetanshi\Partialpro\Controller\Adminhtml\Manage;

use Meetanshi\Partialpro\Controller\Adminhtml\Partial;

class Edit extends Partial
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Meetanshi_Partialpro::partialpayment');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Installments'));
        $resultPage->addBreadcrumb(__('Manage Partial Payment'), __('Manage Installments'));
        $this->_addContent($this->_view->getLayout()->createBlock('\Meetanshi\Partialpro\Block\Adminhtml\Manage\Edit'));
        $this->_view->renderLayout();
    }
}
