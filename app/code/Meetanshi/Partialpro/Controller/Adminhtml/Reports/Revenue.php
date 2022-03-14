<?php

namespace Meetanshi\Partialpro\Controller\Adminhtml\Reports;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
//use Magento\Reports\Controller\Adminhtml\Report\AbstractReport;

class Revenue extends \Magento\Reports\Controller\Adminhtml\Report\AbstractReport
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        Date $dateFilter,
        TimezoneInterface $timezone,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context, $fileFactory, $dateFilter, $timezone);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'Meetanshi_Partialpro::revanuepartialpayment'
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Revenue Report'));
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');
        $gridBlock = $this->_view->getLayout()->getBlock('partialpayment_reports_revenue');
        $this->_initReportAction([$gridBlock, $filterFormBlock]);
        $this->_view->renderLayout();

    }

    protected function _isAllowed()
    {
        return true;
    }
}