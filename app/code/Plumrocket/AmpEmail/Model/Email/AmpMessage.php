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

namespace Plumrocket\AmpEmail\Model\Email;

class AmpMessage extends \Magento\Framework\Mail\Message implements AmpMessageInterface
{
    const TYPE_AMP = 'text/x-amp-html';

    /**
     * @var \Zend\Mail\Message
     */
    protected $zendMessage;

    /**
     * @var string
     */
    private $charset;

    /**
     * @var \Zend\Mail\MessageFactory
     */
    private $zendMailMessageFactory;

    /**
     * @var PartsCollectorInterface
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
    private $currentMimeType = \Zend\Mime\Mime::TYPE_TEXT;

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
     * AmpMessage constructor.
     * TODO: refactor after left support 2.2
     *
     * @param PartsCollectorInterface                                      $partsCollector
     * @param \Plumrocket\AmpEmail\Model\Email\EmailAddressParserInterface $emailAddressParser
     * @param string                                                       $charset
     */
    public function __construct( //@codingStandardsIgnoreLine
        \Plumrocket\AmpEmail\Model\Email\PartsCollectorInterface $partsCollector,
        \Plumrocket\AmpEmail\Model\Email\EmailAddressParserInterface $emailAddressParser,
        string $charset = 'utf-8'
    ) {
        parent::__construct($charset);
        $className = '\Zend\Mail\MessageFactory'; //@codingStandardsIgnoreLine

        $this->zendMailMessageFactory = new $className; //@codingStandardsIgnoreLine
        $this->partsCollector = $partsCollector;
        $this->charset = $charset;
        $this->emailAddressParser = $emailAddressParser;
    }

    /**
     * @param string $mimeType
     * @param mixed  $content
     * @return AmpMessage
     */
    private function setPart(string $mimeType, $content) : self
    {
        $part = $this->partsCollector->createPart($mimeType, $content, $this->getZendMessage()->getEncoding());
        $this->partsCollector->addPartToList($part);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->joinParts()->getZendMessage()->getBody();
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
        $mimeMessage = $this->getMimeMessage();

        $this->partsCollector->applyToMessage($mimeMessage);
        $this->getZendMessage()->setBody($mimeMessage);

        return $this->prepareHeaders();
    }

    /**
     * @return \Zend\Mail\Message
     */
    public function getZendMessage() : \Zend\Mail\Message
    {
        if (null === $this->zendMessage) {
            $this->zendMessage = $this->zendMailMessageFactory::getInstance(['encoding' => $this->charset]);
        }

        return $this->zendMessage;
    }

    /**
     * @return \Zend\Mime\Message
     */
    public function getMimeMessage() : \Zend\Mime\Message
    {
        if (null === $this->mimeMessage) {
            /** @var \Zend\Mime\Message $mimeMessage */
            $this->mimeMessage = new \Zend\Mime\Message(); //@codingStandardsIgnoreLine for compatibility with magento < 2.2.8
        }

        return $this->mimeMessage;
    }

    /**
     * TODO: need refactor after Gmail add support amp + attachment
     * @link https://docs.zendframework.com/zend-mail/message/attachments/
     *
     * @return $this
     */
    private function prepareHeaders() : self
    {
        if ($this->partsCollector->hasAmpForEmail() && $this->partsCollector->getCount() > 1) {
            $headers = $this->getZendMessage()->getHeaders();

            if ($headers->has('content-type')) {
                $header = $headers->get('content-type');
                $header->setType('multipart/alternative');
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRawMessage()
    {
        return $this->joinParts()->getZendMessage()->toString();
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->getZendMessage()->setSubject($subject);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->getZendMessage()->getSubject();
    }

    /**
     * @inheritdoc
     *
     * @deprecated 102.0.1 This function is missing the from name. The
     * setFromAddress() function sets both from address and from name.
     * @see setFromAddress()
     */
    public function setFrom($fromAddress)
    {
        $this->setFromAddress($fromAddress);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setFromAddress($fromAddress, $fromName = null)
    {
        $this->getZendMessage()->setFrom($fromAddress, $fromName);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addTo($toAddress)
    {
        // Save addresses for testing mode - auto
        $this->recipients .= $toAddress . ' ';
        $this->getZendMessage()->addTo($toAddress);
        $this->mainRecipient = $toAddress;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMainRecipient()
    {
        return $this->mainRecipient;
    }

    /**
     * @return array
     */
    public function getSenders()
    {
        $emails = [];
        foreach ($this->getZendMessage()->getFrom() as $email => $address) {
            $emails[] = $email;
        }

        return $emails;
    }

    /**
     * @inheritdoc
     */
    public function addCc($ccAddress)
    {
        $this->getZendMessage()->addCc($ccAddress);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addBcc($bccAddress)
    {
        $this->getZendMessage()->addBcc($bccAddress);
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
    public function setReplyTo($replyToAddress)
    {
        $this->getZendMessage()->setReplyTo($replyToAddress);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setBodyHtml($html)
    {
        return $this->setPart(\Zend\Mime\Mime::TYPE_HTML, $html);
    }

    /**
     * @inheritdoc
     */
    public function setBodyText($text)
    {
        return $this->setPart(\Zend\Mime\Mime::TYPE_TEXT, $text);
    }

    /**
     * @inheritdoc
     */
    public function setBodyAmp(string $ampHtml)
    {
        return $this->setPart(self::TYPE_AMP, $ampHtml);
    }

    /**
     * @inheritdoc
     */
    public function setBodyAttachment($content, $fileName, $fileType, $encoding = '8bit')
    {
        $part = $this->partsCollector->createPart((string)$fileType, $content, $encoding);
        $part->setFileName($fileName)
             ->setDisposition(\Zend\Mime\Mime::DISPOSITION_ATTACHMENT);
        $this->partsCollector->addPartToList($part);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRecipientList() : array
    {
        return $this->emailAddressParser->getValidEmails($this->recipients);
    }
}
