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

interface PartsCollectorInterface
{
    /**
     * Create part for mime type
     *
     * @param string $messageMime
     * @param mixed  $content
     * @param string $encoding
     * @return \Zend\Mime\Part
     */
    public function createPart(string $messageMime, $content, string $encoding) : \Zend\Mime\Part;

    /**
     * Add part to list
     * Will replace part with same mime type
     *
     * @param \Zend\Mime\Part $part
     * @return PartsCollectorInterface
     */
    public function addPartToList(\Zend\Mime\Part $part) : self;

    /**
     * Get unique parts
     *
     * @return \Generator
     */
    public function getList() : \Generator;

    /**
     * Retrieve count of unique parts
     *
     * @return int
     */
    public function getCount() : int;

    /**
     * Add parts to mime message
     * Should applying only once for message
     *
     * TODO: need refactor after Gmail add support amp + attachment
     * @link https://docs.zendframework.com/zend-mail/message/attachments/
     *
     * @param \Zend\Mime\Message $message
     * @return \Zend\Mime\Message
     */
    public function applyToMessage(\Zend\Mime\Message $message) : \Zend\Mime\Message;

    /**
     * Check if exist AMP for Email Part
     *
     * @return bool
     */
    public function hasAmpForEmail() : bool;
}
