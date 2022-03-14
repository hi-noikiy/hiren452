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

namespace Plumrocket\AmpEmail\Api;

interface ResizeImageInterface
{
    /**
     * Resize and crop image for container width and height
     *
     * @param string $image
     * @param int    $containerWidth
     * @param int    $containerHeight
     * @param string $mediaFolder
     * @param string $additionalPath
     * @param bool   $needCrop
     * @return array|bool
     */
    public function execute(
        string $image,
        int $containerWidth,
        int $containerHeight,
        string $mediaFolder,
        string $additionalPath = '',
        bool $needCrop = true
    );
}
