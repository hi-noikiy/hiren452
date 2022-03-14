<?php

namespace Meetanshi\Inquiry\Controller\Adminhtml\Inquiry;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Meetanshi\Inquiry\Model\InquiryFactory;
use Magento\Framework\Exception\LocalizedException;

class Addrow extends Action
{
    private $coreRegistry;
    private $gridFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        InquiryFactory $gridFactory
    )
    {

        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->gridFactory = $gridFactory;
    }

    public function execute()
    {
        $rowId = (int)$this->getRequest()->getParam('id');
        try {
            $rowData = $this->gridFactory->create();
            if ($rowId) {
                $rowData = $rowData->load($rowId);
                $rowTitle = $rowData->getTitle();
                if (!$rowData->getId()) {
                    $this->messageManager->addError(__('row data no longer exist.'));
                    $this->_redirect('meetanshi_inquiry/inquiry/index');
                    return;
                }
            }
            $this->coreRegistry->register('row_data', $rowData);
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $title = $rowId ? __('Edit Dealer Details') . $rowTitle : __('Add Dealer Details');
            $resultPage->getConfig()->getTitle()->prepend($title);
            return $resultPage;
        } catch (\Exception $e) {
            throw new LocalizedException($e->getMessage());
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Meetanshi_Inquiry::inquiry');
    }
}
