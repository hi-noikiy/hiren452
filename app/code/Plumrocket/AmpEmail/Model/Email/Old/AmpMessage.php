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
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
declare(strict_types=1);

namespace Plumrocket\AmpEmail\Model\Email\Old;

use Magento\Framework\Mail\MessageInterface;
use Zend_Mime;

/**
 * Class AmpMessage
 *
 * @deprecated only for compatibility with Zend1 (magento 2.2.7 and below)
 * @package Plumrocket\AmpEmail\Model\Email\Old
 */
class AmpMessage extends \Magento\Framework\Mail\Message implements \Plumrocket\AmpEmail\Model\Email\AmpMessageInterface
{
    const TYPE_AMP = 'text/x-amp-html';

    /**
     * @var string
     */
    private $charset;

    /**
     * @var \Plumrocket\AmpEmail\Model\Email\Old\PartsCollector
     */
    private $partsCollector;

    /**
     * @var \Zend\Mime\Message
     */
    private $mimeMessage;

    /**
     * Forward compatibility
     *
     * @var string
     */
    private $currentMimeType = MessageInterface::TYPE_TEXT;

    /**
     * @var string|null
     */
    private $ampHtml;

    /**
     * @var \Plumrocket\AmpEmail\Model\Email\EmailAddressParserInterface
     */
    private $emailAddressParser;

    /**
     * @var string
     */
    private $recipients = '';

    /**
     * @var string
     */
    private $mainRecipient;

    /**
     * @var array
     */
    private $fromAddresses = [];

    /**
     * AmpMessage constructor.
     *
     * @param PartsCollectorFactory                                        $partsCollector
     * @param \Plumrocket\AmpEmail\Model\Email\EmailAddressParserInterface $emailAddressParser
     * @param string                                                       $charset
     */
    public function __construct(//@codingStandardsIgnoreLine
        \Plumrocket\AmpEmail\Model\Email\Old\PartsCollectorFactory $partsCollector,
        \Plumrocket\AmpEmail\Model\Email\EmailAddressParserInterface $emailAddressParser,
        string $charset = 'utf-8'
    ) {
        $this->partsCollector = $partsCollector->create();
        $this->charset = $charset;

        parent::__construct($charset);
        $this->emailAddressParser = $emailAddressParser;
    }

    /**
     * @param string $mimeType
     * @param mixed  $content
     * @return AmpMessage
     */
    private function setPart(string $mimeType, $content) : self
    {
        $part = $this->partsCollector->createPart($mimeType, $content, $this->charset);
        $this->partsCollector->addPartToList($part);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->joinParts();
    }

    /**
     * Created for compatibility with Mageplaza_EmailAttachments
     *
     * @return $this
     */
    public function setPartsToBody()
    {
        return $this->joinParts();
    }

    /**
     * @return $this
     */
    private function joinParts() : self
    {
        $this->partsCollector->applyToMessage($this);

        return $this->prepareHeaders();
    }

    /**
     * @return \Zend_Mail
     */
    public function getZendMessage() : \Zend_Mail
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addTo($toAddress)
    {
        // Save addresses for testing mode - auto
        $this->recipients .= $toAddress . ' ';
        $this->mainRecipient = $toAddress;
        return parent::addTo($toAddress);
    }

    /**
     * @inheritdoc
     */
    public function getMainRecipient()
    {
        return $this->mainRecipient;
    }

    /**
     * @inheritdoc
     */
    public function getSenders()
    {
        return $this->fromAddresses;
    }

    /**
     * @return \Zend_Mime_Message
     */
    public function getMimeMessage() : \Zend_Mime_Message
    {
        if (null === $this->mimeMessage) {
            /** @var \Zend_Mime_Message $mimeMessage */
            $this->mimeMessage = new \Zend_Mime_Message(); //@codingStandardsIgnoreLine
        }

        return $this->mimeMessage;
    }

    /**
     * @return $this
     */
    private function prepareHeaders() : self
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getRawContent()
    {
        return $this->getRawMessage();
    }

    /**
     * @inheritdoc
     */
    public function getRawMessage()
    {
        return $this->joinParts()->generateMessage();
    }

    /**
     * @inheritdoc
     *
     * @deprecated 102.0.1 This function is missing the from name. The
     * setFromAddress() function sets both from address and from name.
     * @see setFromAddress()
     */
    public function setFrom($fromAddress, $name = null)
    {
        $this->fromAddresses[] = $fromAddress;

        parent::setFrom($fromAddress);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setFromAddress($fromAddress, $fromName = null)
    {
        $this->setFrom($fromAddress, $fromName);
        return $this;
    }

    /**
     * Forward compatibility
     *
     * @inheritdoc
     */
    public function setBody($body)
    {
        $this->setPart($this->currentMimeType, $body);
        return $this;
    }

    /**
     * Forward compatibility
     *
     * @inheritdoc
     */
    public function setMessageType($type)
    {
        $this->currentMimeType = $type;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setBodyHtml($html, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        return $this->setPart(MessageInterface::TYPE_HTML, $html);
    }

    /**
     * @inheritdoc
     */
    public function setBodyText($text, $charset = null, $encoding = \Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        return $this->setPart(MessageInterface::TYPE_TEXT, $text);
    }

    /**
     * @inheritdoc
     */
    public function setBodyAmp(string $ampHtml) : MessageInterface
    {
        $this->ampHtml = $ampHtml;

        return $this->setPart(self::TYPE_AMP, $ampHtml);
    }

    /**
     * @param bool $textOnly
     * @return bool|false|mixed|Zend\Mime\Part|string|\Zend_Mime_Part
     */
    public function getBodyText($textOnly = false)
    {
        return $this->getBodyByType(MessageInterface::TYPE_TEXT, $textOnly);
    }

    /**
     * @param bool $ampOnly
     * @return bool|mixed|Zend\Mime\Part
     */
    public function getBodyAmp($ampOnly = false)
    {
        return $this->getBodyByType(self::TYPE_AMP, $ampOnly);
    }

    /**
     * @param bool $htmlOnly
     * @return bool|false|mixed|Zend\Mime\Part|string|\Zend_Mime_Part
     */
    public function getBodyHtml($htmlOnly = false)
    {
        return $this->getBodyByType(MessageInterface::TYPE_HTML, $htmlOnly);
    }

    /**
     * @param string $type
     * @param bool   $onlyContent
     * @return bool|mixed|Zend\Mime\Part
     */
    private function getBodyByType(string $type, bool $onlyContent = false)
    {
        if ($onlyContent && $this->partsCollector->getPart($type)) {
            $body = $this->partsCollector->getPart($type);
            return $body->getContent();
        }

        return $this->partsCollector->getPart($type);
    }

    /**
     * @inheritdoc
     */
    public function setBodyAttachment($content, $fileName, $fileType, $encoding = '8bit')
    {
        $part = $this->partsCollector->createPart((string)$fileType, $content, $encoding);
        $part->setFileName($fileName)
             ->setDisposition('attachment');
        $this->partsCollector->addPartToList($part);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRecipientList()
    {
        return $this->emailAddressParser->getValidEmails($this->recipients);
    }
}
