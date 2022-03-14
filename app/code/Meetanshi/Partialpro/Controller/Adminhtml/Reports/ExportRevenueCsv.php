<?php

namespace Meetanshi\Partialpro\Controller\Adminhtml\Reports;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Reports\Controller\Adminhtml\Report\Sales;

class ExportRevenueCsv extends Sales
{
    public function execute()
    {
        $fileName = 'RevenueGenerationReport.csv';
        $grid = $this->_view->getLayout()->createBlock('Meetanshi\Partialpro\Block\Adminhtml\Reports\Revenue\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile(), DirectoryList::VAR_DIR);
    }

    protected function _isAllowed()
    {
        return true;
    }
}