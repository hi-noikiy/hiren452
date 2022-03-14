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

use Plumrocket\AmpEmail\Model\Email\AmpMessage as AmpMessageBase;
use Plumrocket\AmpEmail\Model\Email\Old\Zend\Mime\Part as PrampOldPart;

/**
 * Class PartsCollector
 *
 * @deprecated only for compatibility with Zend1 (magento 2.2.7 and below)
 * @package Plumrocket\AmpEmail\Model\Email\Old
 */
class PartsCollector
{
    /**
     * @var \Plumrocket\AmpEmail\Model\Email\Old\Zend\Mime\PartFactory
     */
    private $partFactory;

    /**
     * @var PrampOldPart[]
     */
    private $parts = [];

    /**
     * Keeping object hashes of applied messages
     *
     * @var array
     */
    private $appliedMessages = [];

    /**
     * PartsCollector constructor.
     *
     * @param Zend\Mime\PartFactory $partFactory
     */
    public function __construct(\Plumrocket\AmpEmail\Model\Email\Old\Zend\Mime\PartFactory $partFactory)
    {
        $this->partFactory = $partFactory;
    }

    /**
     * @inheritDoc
     */
    public function createPart(string $messageMime, $content, string $encoding) : PrampOldPart
    {
        /** @var PrampOldPart $part */
        $part = $this->partFactory->create();

        $part->setContent($content);
        $part->setCharset($encoding);
        $part->setType($messageMime);

        return $part;
    }

    /**
     * @inheritDoc
     */
    public function addPartToList(PrampOldPart $part) : self
    {
        $this->parts[$part->getType()] = $part;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getList() : \Generator
    {
        yield from $this->parts;
    }

    /**
     * @inheritDoc
     */
    public function getCount() : int
    {
        return count($this->parts);
    }

    /**
     * @inheritDoc
     */
    public function applyToMessage(\Zend_Mime_Message $message) : \Zend_Mime_Message
    {
        if (! $this->isAppliedToMessage($message)) {
            /** @var PrampOldPart $part */
            foreach ($this->getList() as $part) {
                $message->addPart($part->createZendPart());
            }
            $this->saveApplyAction($message);
        }

        return $message;
    }

    /**
     * @param $type
     * @return bool|\Zend_Mime_Part
     */
    public function getPart($type)
    {
        return isset($this->parts[$type]) ? $this->parts[$type]->createZendPart() : false;
    }

    /**
     * @inheritDoc
     */
    public function hasAmpForEmail() : bool
    {
        return isset($this->parts[AmpMessageBase::TYPE_AMP]);
    }

    /**
     * @param \Zend_Mime_Message $message
     * @return bool
     */
    private function isAppliedToMessage(\Zend_Mime_Message $message) : bool
    {
        return in_array(spl_object_hash($message), $this->appliedMessages, true);
    }

    /**
     * @param \Zend_Mime_Message $message
     * @return PartsCollector
     */
    private function saveApplyAction(\Zend_Mime_Message $message) : self
    {
        $this->appliedMessages[] = spl_object_hash($message);

        return $this;
    }
}
