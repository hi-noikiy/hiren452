<?php

namespace Meetanshi\Inquiry\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Meetanshi\Inquiry\Model\InquiryFactory;

class Inquiry extends Action
{
    protected $coreRegistry;
    protected $resultPageFactory;
    protected $newsFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        InquiryFactory $newsFactory
    )
    {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->newsFactory = $newsFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Meetanshi_Inquiry::Inquiry');
    }

    public function execute()
    {
    }
}
