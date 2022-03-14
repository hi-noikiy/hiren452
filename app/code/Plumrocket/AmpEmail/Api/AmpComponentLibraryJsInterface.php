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

interface AmpComponentLibraryJsInterface
{
    /**
     * Get unique parts
     *
     * @return array
     */
    public function getList() : array;

    /**
     * Detect which native amp component are using in current template
     *
     * @param string $ampEmailContent
     * @return array
     */
    public function detectUsedAmpComponents(string $ampEmailContent) : array;

    /**
     * Either replace placeholder on part or put part in specific place
     *
     * @param string $ampEmailContent
     * @param array  $libraryList
     * @return string
     */
    public function renderIntoEmailContent(string $ampEmailContent, array $libraryList) : string;

    /**
     * Generate html like
     * <script async custom-element="amp-list" src="https://cdn.ampproject.org/v0/amp-list-0.1.js"></script>
     *
     * @param string $type
     * @return string
     */
    public function generateLibraryIncludeHtml(string $type) : string;
}
