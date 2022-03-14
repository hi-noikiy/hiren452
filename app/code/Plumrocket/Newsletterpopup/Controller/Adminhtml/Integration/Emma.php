<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Controller\Adminhtml\Integration;

/**
 * Class Emma
 *
 * @package Plumrocket\Newsletterpopup\Controller\Adminhtml\Integration
 */
class Emma extends \Plumrocket\Newsletterpopup\Controller\Adminhtml\Integration
{
    /**
     * Frontend label for integration
     */
    const RESPONSE_FRONTEND_LABEL = 'Emma';

    /**
     * Test constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Plumrocket\Newsletterpopup\Model\Integration\Emma $serviceModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\Newsletterpopup\Model\Integration\Emma $serviceModel
    ) {
        $this->serviceModel = $serviceModel;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        parent::execute();

        $this->responseFrontendLabel = self::RESPONSE_FRONTEND_LABEL;

        $tempSecretId = htmlspecialchars($this->getRequest()->getParam('api_temp_secret'));
        $accountId = htmlspecialchars($this->getRequest()->getParam('api_account_id'));

        $accountInfo = $this->serviceModel->getAccountInfo($tempSecretId, $accountId);

        if ($accountInfo && isset($accountInfo[0])) {
            return $this->getResultSuccess(__(
                'Success! Your Account is correct. Account ID "%1"',
                $accountInfo[0]
            ), $accountInfo);
        }

        if ($accountInfo && isset($accountInfo['message'])) {
            return $this->getResultError($accountInfo['message']);
        }

        return $this->getResultError();
    }
}