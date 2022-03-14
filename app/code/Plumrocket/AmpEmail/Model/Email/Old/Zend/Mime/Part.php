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

namespace Plumrocket\AmpEmail\Model\Email\Old\Zend\Mime;

use Magento\Framework\Mail\MessageInterface;
use Plumrocket\AmpEmail\Model\Email\Old\AmpMessage;

/**
 * Class Part
 *
 * @deprecated only for compatibility with Zend1 (magento 2.2.7 and below)
 * @package Plumrocket\AmpEmail\Model\Email\Old\Zend\Mime
 */
class Part
{
    /**
     * @var string
     */
    private $type = MessageInterface::TYPE_TEXT;

    /**
     * @var string|null
     */
    private $content;

    /**
     * @var string|null
     */
    private $charset;

    /**
     * @var string|null
     */
    private $fileName;

    /**
     * @var string|null
     */
    private $disposition;

    /**
     * @return \Zend_Mime_Part
     */
    public function createZendPart() : \Zend_Mime_Part
    {
        $zendPart = new \Zend_Mime_Part($this->getContent()); //@codingStandardsIgnoreLine

        if ($this->getCharset()) {
            $zendPart->charset = $this->getCharset();
        }

        if ($this->getFileName()) {
            $zendPart->filename = $this->getFileName();
        }

        if ($this->getDisposition()) {
            $zendPart->disposition = $this->getDisposition();
        }

        if ($this->getType()) {
            $zendPart->type = $this->getType();
        }

        $isStringPart = in_array(
            $this->getType(),
            [MessageInterface::TYPE_TEXT, MessageInterface::TYPE_HTML, AmpMessage::TYPE_AMP],
            true
        );

        if ($isStringPart) {
            $zendPart->encoding = false;
        }

        return $zendPart;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Part
     */
    public function setType($type) : self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content) : self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $encoding
     * @return $this
     */
    public function setCharset(string $encoding) : self
    {
        $this->charset = $encoding;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param $fileName
     * @return $this
     */
    public function setFileName($fileName) : self
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDisposition()
    {
        return $this->disposition;
    }

    /**
     * @param string $disposition
     * @return $this
     */
    public function setDisposition(string $disposition) : self
    {
        $this->disposition = $disposition;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRawContent()
    {
        return $this->getContent();
    }
}
