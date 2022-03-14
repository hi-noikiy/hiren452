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

namespace Plumrocket\Smtp\Model;

class Log extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var string
     */
    const ELEMENT_SUBJECT = 'Subject';

    /**
     * @var string
     */
    const ELEMENT_FROM = 'From';

    /**
     * @var string
     */
    const ELEMENT_TO = 'To';

    /**
     * @var string
     */
    const ELEMENT_APP = 'append';

    /**
     * @var \Plumrocket\Smtp\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * Log constructor.
     *
     * @param \Magento\Framework\Escaper                                   $escaper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                  $date
     * @param \Plumrocket\Smtp\Helper\Data                                 $dataHelper
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Plumrocket\Smtp\Helper\Data $dataHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->escaper = $escaper;
        $this->date = $date;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Email Init
     */
    protected function _construct() //@codingStandardsIgnoreLine default magento functionality
    {
        $this->_init(ResourceModel\Log::class);
    }

    /**
     * @param $messageObject
     * @param $status
     * @param $debugInfo
     * @return $this
     */
    public function addLog($messageObject, $status, $debugInfo)
    {
        if ($this->dataHelper->newVersion()) {
            $this->saveLogForNewVersion($messageObject, $status, $debugInfo);
        } else {
            $this->saveLogForOldVersion($messageObject, $status, $debugInfo);
        }

        return $this;
    }

    /**
     * @param $messageObject
     * @param $status
     * @param $debugInfo
     */
    private function saveLogForNewVersion($messageObject, $status, $debugInfo)
    {
        $emailHtmlContent = $this->escaper->escapeHtml($messageObject->getBodyText());
        $this->setBody($emailHtmlContent);

        if ($messageObject->getSubject()) {
            $this->setSubject($messageObject->getSubject());
        }

        $fromEmail = $messageObject->getFrom();

        if (count($fromEmail)) {
            $fromEmail->rewind();
            $this->setEmailFrom($fromEmail->current()->getEmail());
        }

        $toEmailArray = [];
        foreach ($messageObject->getTo() as $toEmailAddress) {
            $toEmailArray[] = $toEmailAddress->getEmail();
        }

        $this->setEmailTo(implode(',', $toEmailArray));

        $this->setStatus($status);
        $this->setSentAt($this->date->gmtDate());
        $this->setDebugLog($debugInfo)->save();
    }

    /**
     * @param $messageObject
     * @param $status
     * @param $debugInfo
     */
    private function saveLogForOldVersion($messageObject, $status, $debugInfo)
    {
        $emailHtmlBody = $messageObject->getBodyHtml();

        if (is_object($emailHtmlBody)) {
            $emailHtmlContent = $this->escaper->escapeHtml($emailHtmlBody->getRawContent());
        } else {
            $emailHtmlContent = $this->escaper->escapeHtml($messageObject->getBody()->getRawContent());
        }

        $this->setBody($emailHtmlContent);

        $messageHeaders = $messageObject->getHeaders();

        if (isset($messageHeaders[self::ELEMENT_SUBJECT][0])) {
            $this->setSubject($messageHeaders[self::ELEMENT_SUBJECT][0]);
        }

        if (isset($messageHeaders[self::ELEMENT_FROM][0])) {
            $this->setEmailFrom($messageHeaders[self::ELEMENT_FROM][0]);
        }

        if (isset($messageHeaders[self::ELEMENT_TO])) {
            $toEmails = $messageHeaders[self::ELEMENT_TO];
            if (isset($toEmails[self::ELEMENT_APP])) {
                unset($toEmails[self::ELEMENT_APP]);
            }

            $this->setEmailTo(implode(', ', $toEmails));
        }

        $this->setStatus($status);
        $this->setSentAt($this->date->gmtDate());
        $this->setDebugLog($debugInfo)->save();
    }
}
