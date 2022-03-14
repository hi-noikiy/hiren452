<?php

namespace Meetanshi\Partialpro\Controller\Orangeivory;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Meetanshi\Partialpro\Helper\Data;

class Redirect extends Action\Action
{
    protected $resultPageFactory;
    protected $partialpaymentCron;
    protected $helper;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Data $helper
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->helper->isModuleEnabled()) {
            $inst_id = $this->getRequest()->getParam('inst_id');
            try {
                if ($inst_id > 0) {
                    $resultPage = $this->resultPageFactory->create();
                    $resultPage->getConfig()->getTitle()->set(__('Orange Ivory Pay Installment'));
                    return $resultPage;

                } else {
                    $this->_redirect('partialpayment/account/index');
                }
            } catch (\Exception $e) {

                \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());
            }
        }
    }
}
