<?php

namespace Meetanshi\Partialpro\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Meetanshi\Partialpro\Model\Installments;
use Magento\Framework\Controller\Result\JsonFactory;

class Duedatechange extends Action
{
    protected $installments;
    private $resultJsonFactory;

    public function __construct(
        Context $context,
        Installments $installments,
        JsonFactory $resultJsonFactory
    )
    {
        $this->installments = $installments;
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $response = [];
        $response['result'] = false;
        if ($data) {
            try {
                if (isset($data['newdate']) && isset($data['installmentid'])) {
                    $installment = $this->installments->load($data['installmentid']);
                    if ($installment->getId()) {
                        $installment->setInstallmentDueDate($data['newdate'])->save();
                        $response['result'] = true;
                    } else {
                        $response['result'] = 'false';
                        $response['data'] = 'Unable to find payment id';
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __($e->getMessage())
                );
            }

            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}
