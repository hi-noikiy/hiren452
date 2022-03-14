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

namespace Plumrocket\AmpEmail\Helper;

use Plumrocket\AmpEmail\Model\Email\AmpTemplateInterface;

/**
 * Class Data
 *
 * @package Plumrocket\AmpEmail\Helper
 */
class Data extends Main
{
    /**
     * Section name for configs
     */
    const SECTION_ID = 'prampemail';

    const AMP_CMS_SOCIAL_BLOCK_IDENTIFIER = 'pr_amp_email_social_buttons';

    /**
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
     * @param null $store
     * @return bool
     */
    public function moduleEnabled($store = null)
    {
        return (bool) $this->getConfig($this->_configSectionId . '/general/enabled', $store);
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
                $connection->quoteInto('path = ?', $this->_configSectionId  . '/general/enabled')
            ]
        );

        $config->setDataByPath($this->_configSectionId  . '/general/enabled', 0);
        $config->save();
    }

    /**
     * @param bool $withEmptyLine
     * @return array
     */
    public function getAmpEmailTemplateStatuses(bool $withEmptyLine = false) : array
    {
        $statuses = [
            AmpTemplateInterface::AMP_EMAIL_STATUS_DISABLED => 'Disabled',
            AmpTemplateInterface::AMP_EMAIL_STATUS_LIVE => 'Enabled - Live',
            AmpTemplateInterface::AMP_EMAIL_STATUS_SANDBOX => 'Enabled - Sandbox',
        ];

        if ($withEmptyLine) {
            $statuses = [null => null] + $statuses;
        }

        return $statuses;
    }

    /**
     * Check if installed and enabled module Plumrocket_Bestsellers
     *
     * @return bool
     */
    public function isEnabledModuleBestsellers() : bool
    {
        return 2 === $this->moduleExists('Bestsellers');
    }
}
