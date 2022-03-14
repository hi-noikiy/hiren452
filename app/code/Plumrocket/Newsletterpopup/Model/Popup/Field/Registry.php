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

namespace Plumrocket\Newsletterpopup\Model\Popup\Field;

use Plumrocket\Newsletterpopup\Api\PopupFieldsRegistryInterface;

/**
 * @since v3.10.0
 */
class Registry implements PopupFieldsRegistryInterface
{
    /**
     * @var array[]
     */
    private $fields;

    /**
     * AdditionalLocationRegistry constructor.
     *
     * @param array[] $fields
     */
    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }

    /**
     * @return array[]
     */
    public function getList(): array
    {
        return $this->fields;
    }
}
