<?php

namespace Meetanshi\Inquiry\Controller\Adminhtml\Inquiry;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Meetanshi\Inquiry\Model\InquiryFactory;
use Meetanshi\Inquiry\Helper\Data;

class Delete extends Action
{
    private $coreRegistry;
    private $gridFactory;
    protected $helper;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        InquiryFactory $gridFactory,
        Data $helper
    )
    {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->gridFactory = $gridFactory;
        $this->helper = $helper;
    }

    public function execute()
    {
        $rowId = (int)$this->getRequest()->getParam('id');
        $rowData = $this->gridFactory->create();
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            if (!$rowData->getDealerId()) {
                $this->messageManager->addError(__('Customer data no longer exist.'));
                return $this->_redirect('meetanshi_inquiry/inquiry/index');
            } else {
                try {
                    $rowData->delete();
                    $this->messageManager->addSuccess(__('The Dealer has been deleted.'));
                    $this->_redirect('meetanshi_inquiry/inquiry/index');
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirectReferer();
                }
            }
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Meetanshi_Inquiry::inquiry');
    }
}
