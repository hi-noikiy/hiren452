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

namespace Plumrocket\Smtp\Plugin\Framework\Mail;

use Closure;
use Exception;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Phrase;
use ReflectionClass;
use Zend\Mail\Message;
use Zend_Exception;
use Plumrocket\Smtp\Model\Config\Source\Status;

class TransportPlugin
{
    /**
     * @var string
     */
    const NULL_STRING = '0';

    /**
     * @var \Plumrocket\Smtp\Model\Mail
     */
    private $mail;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Plumrocket\Smtp\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\Smtp\Model\LogFactory
     */
    private $emailLogFactory;

    /**
     * TransportPlugin constructor.
     *
     * @param \Plumrocket\Smtp\Model\Mail       $mail
     * @param \Magento\Framework\Registry       $registry
     * @param \Plumrocket\Smtp\Helper\Data      $dataHelper
     * @param \Plumrocket\Smtp\Model\LogFactory $emailLogFactory
     */
    public function __construct(
        \Plumrocket\Smtp\Model\Mail $mail,
        \Magento\Framework\Registry $registry,
        \Plumrocket\Smtp\Helper\Data $dataHelper,
        \Plumrocket\Smtp\Model\LogFactory $emailLogFactory
    ) {
        $this->mail = $mail;
        $this->registry = $registry;
        $this->dataHelper = $dataHelper;
        $this->emailLogFactory = $emailLogFactory;
    }

    /**
     * @param TransportInterface $subject
     * @param Closure            $proceed
     * @return mixed
     * @throws MailException
     * @throws Zend_Exception
     */
    public function aroundSendMessage(
        TransportInterface $subject,
        Closure $proceed
    ) {
        $storeId = $this->registry->registry('plumrocket_smtp_store_id');

        if (! $this->dataHelper->moduleEnabled($storeId)) {
            return $proceed();
        }

        $message = $this->getMessage($subject);

        if (! $message) {
            return $proceed();
        }

        $status = Status::STATUS_SUCCESS;
        $debugInfo = __('Email was sent successfully.');

        if ($this->dataHelper->newVersion()) {
            $message = Message::fromString($message->getRawMessage())->setEncoding('utf-8');
        }

        $transportObject = $this->mail->getTransport($storeId);

        try {
            if ($this->dataHelper->enableEmailSending($storeId)) {
                $transportObject->send($message);
            }

            if ($this->dataHelper->newVersion()) {
                $this->setBodyMultipart($subject, $message);
            }
        } catch (Exception $exception) {
            $status = Status::STATUS_ERROR;
            $debugInfo = $exception->getMessage();
        }

        $this->saveEmailLog($message, $storeId, $status, $debugInfo);

        if ($status === Status::STATUS_ERROR) {
            throw new MailException(new Phrase($debugInfo));
        }
    }

    /**
     * @param $message
     * @param $store
     */
    private function saveEmailLog($message, $store, $status, $debugInfo)
    {
        if ($this->dataHelper->logEnabled($store)) {
            $this->emailLogFactory->create()->addLog($message, $status, $debugInfo);
        }
    }

    /**
     * @param $transport
     * @return mixed
     * @throws MailException
     */
    private function getMessage($transport)
    {
        if ($this->dataHelper->canUseReflectionClass()) {
            try {
                $reflectionObject = new ReflectionClass($transport);
                $messageObject = $reflectionObject->getProperty('_message');
            } catch (Exception $exception) {
                throw new MailException(new Phrase($exception->getMessage()), $exception);
            }

            $messageObject->setAccessible(true);

            return $messageObject->getValue($transport);
        }

        return $transport->getMessage();
    }

    /**
     * @param $subject
     * @param $message
     * @throws MailException
     */
    private function setBodyMultipart($subject, $message)
    {
        $messageTmp = $this->getMessage($subject);

        if ($messageTmp && is_object($messageTmp)) {
            $emailBodyObject = $messageTmp->getBody();

            if (is_object($emailBodyObject) && $emailBodyObject->isMultiPart()) {
                $message->setBody($emailBodyObject->getPartContent(self::NULL_STRING));
            }
        }
    }
}
