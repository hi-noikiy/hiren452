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

namespace Plumrocket\AmpEmail\Model\Template\Config;

use Magento\Framework\Serialize\SerializerInterface;

/**
 * Provides amp email templates configuration
 */
class Data extends \Magento\Framework\Config\Data
{
    /**
     * Data constructor.
     *
     * @param Reader                                   $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param SerializerInterface                      $serializer
     * @param string                                   $cacheId
     */
    public function __construct(
        \Plumrocket\AmpEmail\Model\Template\Config\Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        $cacheId = 'pramp_email_templates'
    ) {
        parent::__construct($reader, $cache, $cacheId, $serializer);
    }
}
