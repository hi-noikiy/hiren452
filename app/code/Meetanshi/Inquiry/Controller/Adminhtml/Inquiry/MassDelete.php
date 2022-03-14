<?php

namespace Meetanshi\Inquiry\Controller\Adminhtml\Inquiry;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Meetanshi\Inquiry\Model\ResourceModel\Inquiry\CollectionFactory;
use Meetanshi\Inquiry\Helper\Data;

class MassDelete extends Action
{
    protected $filter;
    protected $collectionFactory;
    protected $helper;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Data $helper
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $recordDeleted = 0;
            foreach ($collection->getItems() as $record) {
                $record->setId($record->getDealerId());
                $record->delete();
                $recordDeleted++;
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $recordDeleted));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('meetanshi_inquiry/inquiry/index');
        } catch (\Exception $e) {
            $this->helper->printLog($e->getMessage());
            $this->messageManager->addError(__('We can’t process your request right now. Sorry'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('meetanshi_inquiry/inquiry/index');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Meetanshi_Inquiry::inquiry');
    }
}
