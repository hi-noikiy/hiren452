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

use Zend\Mime\Mime;
use Plumrocket\AmpEmail\Model\Email\AmpMessage;

class MimeTypeSorter implements MimeTypeSorterInterface
{
    /**
     * Sort email message parts by mime types
     *
     * @param \Zend\Mime\Part[] $parts
     * @return \Zend\Mime\Part[]
     */
    public function sort(array $parts) : array
    {
        uasort($parts, [$this, 'sortParts']);

        return $parts;
    }

    /**
     * @param \Zend\Mime\Part $firstPart
     * @param \Zend\Mime\Part $secondPart
     * @return int
     */
    private function sortParts($firstPart, $secondPart)
    {
        if (Mime::TYPE_TEXT === $firstPart->getType()) {
            return -1;
        }

        if ($firstPart->getType() === AmpMessage::TYPE_AMP) {
            return Mime::TYPE_TEXT === $secondPart->getType() ? 1 : -1;
        }

        if ($firstPart->getType() === Mime::TYPE_HTML) {
            return in_array($secondPart->getType(), [Mime::TYPE_TEXT, AmpMessage::TYPE_AMP], true) ? 1 : -1;
        }

        return 1;
    }
}
