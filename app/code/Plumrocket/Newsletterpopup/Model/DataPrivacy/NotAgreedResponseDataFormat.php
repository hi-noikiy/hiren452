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
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Newsletterpopup\Model\DataPrivacy;

use Magento\Framework\Message\MessageInterface;

/**
 * Create specific json response for our js logic
 *
 * @since v3.10.0
 */
class NotAgreedResponseDataFormat
{
    /**
     * We use type "error" because our js know how to work only with this type
     *
     * @param $message
     * @return array
     */
    public function execute($message): array
    {
        return [
            'error' => 1,
            'messages' => [
                MessageInterface::TYPE_ERROR => [
                    $message
                ]
            ],
            'hasSuccessTextPlaceholders' => false,
        ];
    }
}
