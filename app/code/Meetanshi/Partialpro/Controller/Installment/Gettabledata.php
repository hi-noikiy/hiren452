<?php

namespace Meetanshi\Partialpro\Controller\Installment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Framework\Controller\Result\JsonFactory;

class Gettabledata extends Action
{
    private $helper;
    private $resultJsonFactory;

    public function __construct(Context $context, Data $helper, JsonFactory $resultJsonFactory)
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $productId = $this->getRequest()->getParam('productId');
        $numOfInstallment = $this->getRequest()->getParam('numOfInstallment');

        $result->setData(['html' => $this->helper->getInstallmentTable($productId, $numOfInstallment)]);
        return $result;
    }
}

