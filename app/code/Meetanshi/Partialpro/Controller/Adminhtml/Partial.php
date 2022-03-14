<?php

namespace Meetanshi\Partialpro\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

abstract class Partial extends Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Meetanshi_Partialpro::partialpayment')->_addBreadcrumb(__('Partial Payment'), __('Partial Payment'));
        return $this;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
