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

namespace Plumrocket\AmpEmail\Model\Testing;

/**
 * Class Template
 * Disable load methods
 *
 * @package Plumrocket\AmpEmail\Model\Testing
 */
class Template extends \Magento\Email\Model\Template
{
    /**
     * @param string $templateId
     * @return $this|\Magento\Email\Model\Template
     */
    public function loadDefault($templateId)
    {
        return $this;
    }

    /**
     * @param int  $modelId
     * @param null $field
     * @return $this|\Magento\Email\Model\Template
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }
}
