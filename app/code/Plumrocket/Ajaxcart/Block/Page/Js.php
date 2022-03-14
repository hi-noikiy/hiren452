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

namespace Plumrocket\Ajaxcart\Block\Page;

/**
 * Class Js
 *
 * @package Plumrocket\Ajaxcart\Block
 */
class Js extends \Plumrocket\Ajaxcart\Block\WorkMode\Js
{
    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->dataHelper->moduleEnabled()
            && $this->dataHelper->isManualMode()
        ) {
            return parent::toHtml();
        }

        return '';
    }
}
