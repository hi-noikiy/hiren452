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
 * @package     Plumrocket_ShippingTracking
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ShippingTracking\Helper;

/**
 * Class Data Helper
 */
class Data extends \Plumrocket\ShippingTracking\Helper\Main
{
    /**
     * Config section id
     */
    const SECTION_ID = 'prshippingtracking';

    /**
     * @var string
     */
    protected $_configSectionId = self::SECTION_ID;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Magento\Config\Model\ConfigFactory
     */
    private $configFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param Config                                    $config
     * @param \Magento\Config\Model\ConfigFactory       $configFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Plumrocket\ShippingTracking\Helper\Config $config,
        \Magento\Config\Model\ConfigFactory $configFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        parent::__construct($objectManager, $context);
        $this->config = $config;
        $this->configFactory = $configFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param null $store
     * @return bool
     */
    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId . '/general/enabled', $store);
    }

    /**
     * @return Config
     */
    public function getSysConfig()
    {
        return $this->config;
    }
}