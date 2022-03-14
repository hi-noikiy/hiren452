<?php

namespace Meetanshi\Partialpro\Controller\Authorize;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Meetanshi\Partialpro\Model\AuthorizeCim;
use Magento\Framework\Controller\ResultFactory;

class createProfile extends Action
{
    protected $checkoutSession;
    protected $authorizeModel;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        AuthorizeCim $authorizeModel
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->authorizeModel = $authorizeModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $sessionId = $this->checkoutSession->getSessionId();
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $ccDetail = $this->getRequest()->getParams();
        $ccDetail = $ccDetail['result'];

        $data = [
            'session_id' => $sessionId,
            'cc_number' => $ccDetail['ccNumber'],
            'cc_exp_month' => $ccDetail['expMonth'],
            'cc_exp_year' => $ccDetail['expYear']
        ];

        if (array_key_exists('ccId', $ccDetail)) {
            $data['cc_id'] = $ccDetail['ccId'];
        } else {
            $data['cc_id'] = 0;
        }

        $quote = $this->checkoutSession->getQuote();
        try {

            $response = $this->authorizeModel->createCustomerProfile($data, $quote);

            if ($response['result']) {
                $this->checkoutSession->setCustomerProfileId($response['customerProfileId']);
                $this->checkoutSession->setPaymentProfileId($response['paymentProfileId']);

            }
            $resultJson->setData($response);

            return $resultJson;
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while trying to process creating Authorize profile request.')
            );
        }
    }
}
