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
 * @package     Plumrocket_Bestsellers
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
declare(strict_types=1);

namespace Plumrocket\Bestsellers\Helper;

class Data extends Main
{
    /**
     * Section name for configs
     */
    const SECTION_ID = 'pr_bestsellers';

    /**
     * @deprecated
     * @var string
     */
    protected $_configSectionId = self::SECTION_ID; //@codingStandardsIgnoreLine

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Config\Model\ConfigFactory
     */
    private $configFactory;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Config\Model\ConfigFactory       $configFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Config\Model\ConfigFactory $configFactory
    ) {
        parent::__construct($objectManager, $context);
        $this->resourceConnection = $resourceConnection;
        $this->configFactory = $configFactory;
    }

    /**
     * Check if module enabled for current|specific store
     *
     * @param null $store
     * @return bool
     */
    public function moduleEnabled($store = null) : bool
    {
        return (bool)$this->getConfig(self::SECTION_ID . '/general/enabled', $store);
    }

    /**
     * @return void
     */
    public function disableExtension()
    {
        /** @var \Magento\Config\Model\Config $config */
        $config = $this->configFactory->create();
        $connection = $this->resourceConnection->getConnection('core_write');

        $connection->delete(
            $this->resourceConnection->getTableName('core_config_data'),
            [
                $connection->quoteInto('path = ?', self::SECTION_ID  . '/general/enabled')
            ]
        );

        $config->setDataByPath(self::SECTION_ID  . '/general/enabled', 0);
        $config->save();
    }
}
