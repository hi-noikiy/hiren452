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
 * @package     Plumrocket SMTP
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Smtp\Controller\Adminhtml\Send;

use Exception;

class Email extends \Magento\Backend\App\Action
{
    /**
     * @var \Plumrocket\Smtp\Helper\Send\
     */
    private $emailHelper;

    /**
     * @var \Plumrocket\Smtp\Helper\Data
     */
    private $dataHelper;

    /**
     * @var array
     */
    private $errorMessage = [];

    /**
     * Email constructor.
     *
     * @param \Plumrocket\Smtp\Helper\Send\Email  $emailHelper
     * @param \Plumrocket\Smtp\Helper\Data        $dataHelper
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Plumrocket\Smtp\Helper\Send\Email $emailHelper,
        \Plumrocket\Smtp\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->emailHelper = $emailHelper;
        $this->dataHelper = $dataHelper;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $postData = $this->getRequest()->getParams();

        try {
            if ($this->validateData($postData)) {
                $data['message'] = __('Email was sent successfully.');
                $this->dataHelper->setConnectionData($postData);
                $this->emailHelper->sendTestEmail($postData['template'], $postData['from'], $postData['to']);
            } else {
                $data['message'] = __('Error(s)') . ': ' . implode(' ', $this->errorMessage);
            }
        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
        }

        $this->getResponse()->setBody(json_encode($data));
    }

    /**
     * @param $postData
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    private function validateData($postData)
    {
        $isValid = true;

        if (!\Zend_Validate::is(trim($postData['to']), 'EmailAddress')) {
            $isValid = false;
            $this->errorMessage[] = __('Email address is invalid.');
        }

        if (!\Zend_Validate::is($postData['host'], 'NotEmpty')) {
            $isValid = false;
            $this->errorMessage[] = __('SMTP host is invalid.');
        }

        if (!\Zend_Validate::is($postData['port'], 'NotEmpty')) {
            $isValid = false;
            $this->errorMessage[] = __('SMTP port is invalid.');
        }

        return $isValid;
    }
}
