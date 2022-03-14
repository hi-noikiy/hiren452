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

namespace Plumrocket\Newsletterpopup\Api;

/**
 * You can pass additional fields via DI
 * either by pass into constructor or using After plugin on getList method
 *
 * They'll installed during recurring step of setup:upgrade
 *
 * @since v3.10.0
 */
interface PopupFieldsRegistryInterface
{
    /**
     * Format
     * [
     *      name => [
     *          'label' => string,
     *          'enable' => int, - 0,1
     *          'sort_order' => int,
     *          'popup_id' => int, - put 0 for all popups
     *      ]
     * ]
     *
     * @param array $fields
     */
    public function __construct(array $fields = []);

    /**
     * @return array[]
     */
    public function getList(): array;
}
