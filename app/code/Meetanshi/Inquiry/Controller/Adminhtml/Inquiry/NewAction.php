<?php

namespace Meetanshi\Inquiry\Controller\Adminhtml\Inquiry;

use Meetanshi\Inquiry\Controller\Adminhtml\Inquiry;

class NewAction extends Inquiry
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
