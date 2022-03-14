<?php

namespace Meetanshi\Partialpro\Controller\Tagpay;

use Magento\Framework\App\Action;
use Meetanshi\Partialpro\Helper\Data;

class Accept extends Action\Action
{
    protected $helper;

    public function __construct(
        Action\Context $context,
        Data $helper
    )
    {
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->helper->isModuleEnabled()) {
            //$params = $this->request->getParams();
            $this->_redirect('partialpayment/account/index/');
        }
    }
}

