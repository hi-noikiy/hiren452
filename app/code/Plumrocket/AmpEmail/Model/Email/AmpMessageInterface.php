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

use Magento\Framework\Mail\MessageInterface;

interface AmpMessageInterface extends MessageInterface
{
    /**
     * Setter for AMP content
     *
     * @param string $ampHtml
     * @return MessageInterface
     */
    public function setBodyAmp(string $ampHtml);

    /**
     * TODO: change comment after Gmail add support amp + attachment
     * Attention!
     * For now (19.06.2019) Gmail doesn't show attachment with amp
     * You must disable either amp for email or attachment.
     *
     * Add the attachment mime part to the message.
     * Created for compatibility with Mageplaza_EmailAttachments
     *
     * @param string $content
     * @param string $fileName
     * @param string $fileType
     * @param string $encoding
     * @return $this
     */
    public function setBodyAttachment($content, $fileName, $fileType, $encoding = '8bit');

    /**
     * Set mail message body in HTML format.
     *
     * @param string $html
     * @return $this
     */
    public function setBodyHtml($html);

    /**
     * Set mail message body in text format.
     *
     * @param string $text
     * @return $this
     */
    public function setBodyText($text);

    /**
     * Get message source code.
     *
     * @return string
     */
    public function getRawMessage();

    /**
     * Retrieve list of recipient emails
     *
     * @return array
     */
    public function getRecipientList();

    /**
     * Probably this is customer/guest/admin email address
     *
     * @return string
     */
    public function getMainRecipient();

    /**
     * Retrieve list of senders for future validation
     *
     * @return array
     */
    public function getSenders();
}
