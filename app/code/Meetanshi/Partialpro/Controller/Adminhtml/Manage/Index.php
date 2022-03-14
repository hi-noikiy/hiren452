<?php

namespace Meetanshi\Partialpro\Controller\Adminhtml\Manage;

use Meetanshi\Partialpro\Controller\Adminhtml\Partial;

class Index extends Partial
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Manage Partial Payment Orders')));
        return $resultPage;
    }
}