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

class PartsCollector implements PartsCollectorInterface
{
    /**
     * @var \Zend\Mime\Part[]
     */
    private $parts = [];

    /**
     * Keeping object hashes of applied messages
     *
     * @var array
     */
    private $appliedMessages = [];

    /**
     * @var MimeTypeSorterInterface
     */
    private $mimeTypeSorter;

    /**
     * PartsCollector constructor.
     *
     * @param MimeTypeSorterInterface $mimeTypeSorter
     */
    public function __construct(
        MimeTypeSorterInterface $mimeTypeSorter
    ) {
        $this->mimeTypeSorter = $mimeTypeSorter;
    }

    /**
     * @inheritDoc
     */
    public function createPart(string $messageMime, $content, string $encoding) : \Zend\Mime\Part
    {
        /** @var \Zend\Mime\Part $part */
        $part = $this->newZendMimePart();

        $part->setContent($content);
        $part->setCharset($encoding);
        $part->setType($messageMime);

        return $part;
    }

    /**
     * @inheritDoc
     */
    public function addPartToList(\Zend\Mime\Part $part) : PartsCollectorInterface
    {
        $this->parts[$part->getType()] = $part;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getList() : \Generator
    {
        yield from $this->mimeTypeSorter->sort($this->parts);
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
    public function applyToMessage(\Zend\Mime\Message $message) : \Zend\Mime\Message
    {
        if (! $this->isAppliedToMessage($message)) {
            /** @var \Zend\Mime\Part $part */
            foreach ($this->getList() as $part) {
                $message->addPart($part);
            }
            $this->saveApplyAction($message);
        }

        return $message;
    }

    /**
     * @inheritDoc
     */
    public function hasAmpForEmail() : bool
    {
        return isset($this->parts[AmpMessage::TYPE_AMP]);
    }

    /**
     * @param \Zend\Mime\Message $message
     * @return bool
     */
    private function isAppliedToMessage(\Zend\Mime\Message $message) : bool
    {
        return in_array(spl_object_hash($message), $this->appliedMessages, true);
    }

    /**
     * @param \Zend\Mime\Message $message
     * @return $this
     */
    private function saveApplyAction(\Zend\Mime\Message $message) : self
    {
        $this->appliedMessages[] = spl_object_hash($message);

        return $this;
    }

    /**
     * @return \Zend\Mime\Part
     */
    private function newZendMimePart()
    {
        return new \Zend\Mime\Part(); //@codingStandardsIgnoreLine can move into construct after left support Zend1
    }
}
