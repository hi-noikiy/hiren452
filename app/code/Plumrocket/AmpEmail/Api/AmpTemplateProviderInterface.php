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

use Plumrocket\AmpEmail\Model\Email\AmpTemplateInterface;

/**
 * Class AmpTemplateProvider
 *
 * Load and cache amp templates
 */
interface AmpTemplateProviderInterface
{
    /**
     * @param string|int $templateId
     * @param bool       $forceReload
     * @return \Plumrocket\AmpEmail\Model\Email\AmpTemplateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTemplate($templateId, bool $forceReload = false) : AmpTemplateInterface;

    /**
     * Check if exist AMP content for specific template
     *
     * @param $templateId
     * @return bool
     */
    public function isExistAmpForEmailById($templateId) : bool;
}
