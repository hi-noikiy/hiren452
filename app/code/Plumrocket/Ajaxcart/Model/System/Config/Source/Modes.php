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
 * @package     Plumrocket Ajaxcart v2.x.x
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Model\System\Config\Source;

/**
 * Class Modes
 *
 * @package Plumrocket\Ajaxcart\Model\System\Config\Source
 */
class Modes
{
    /**
     * @var integer
     */
    const AUTOMATIC = 0;

    /**
     * @var integer
     */
    const MANUAL = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach($this->toArray() as $value => $label) {
            $result[] = ['value' => $value, 'label'=> $label];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::AUTOMATIC => __('Automatic'),
            self::MANUAL => __('Manual'),
        ];
    }
}
