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
 * @package   Plumrocket_AutoInvoiceShipment
 * @copyright Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AutoInvoiceShipment\Helper;

/**
 * Class Data
 *
 * @package Plumrocket\AutoInvoiceShipment\Helper
 */
class Data extends Main
{
    /**
     * Needed for Plumrocket Base and for function "getConfigPath"
     *
     * @var string
     */
    protected $_configSectionId = 'prautoinvoiceshipment';

    /**
     * @var \Magento\Config\Model\Config
     */
    protected $config;

    /**
     * Needed for disable modules
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Magento\Config\Model\Config              $config
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface   $objectManager,
        \Magento\Framework\App\Helper\Context       $context,
        \Magento\Config\Model\Config                $config,
        \Magento\Framework\App\ResourceConnection   $resourceConnection
    ) {
        parent::__construct($objectManager, $context);
        $this->resourceConnection   = $resourceConnection;
        $this->config               = $config;
    }

    /**
     * Is module enabled
     *
     * @param  string | int $store
     * @return bool
     */
    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId . '/general/enabled', $store);
    }

    /**
     * Send invoice email automatically
     *
     * @param  string | int $store
     * @return bool
     */
    public function autoSendInvoiceEmail($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId . '/general/invoice_send_auto', $store);
    }

    /**
     * Send shipment email automatically
     *
     * @param  string | int $store
     * @return bool
     */
    public function autoSendShipmentEmail($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId . '/general/shipment_send_auto', $store);
    }

    /**
     * Disable extension
     */
    public function disableExtension()
    {
        $connection = $this->resourceConnection->getConnection('core_write');
        $connection->delete(
            $this->resourceConnection->getTableName('core_config_data'),
            [$connection->quoteInto('path = ?', $this->_configSectionId . '/general/enabled')]
        );

        $this->config->setDataByPath($this->_configSectionId . '/general/enabled', 0);
        $this->config->save();
    }

    /**
     * Check if invoice created manually
     *
     * @return bool
     */
    public function isInvoiceCreatedManually()
    {
        return ($this->_getRequest()->getModuleName() === 'sales'
            && $this->_getRequest()->getActionName() === 'save'
            && $this->_getRequest()->getParam('invoice')
        );
    }

    /**
     * Check if shipment created manually
     *
     * @return bool
     */
    public function isShipmentCreatedManually()
    {
        return ($this->_getRequest()->getModuleName() === 'admin'
            && $this->_getRequest()->getActionName() === 'save'
            && $this->_getRequest()->getParam('shipment')
        );
    }

    /**
     * To use multi source inventory
     *
     * @return bool
     */
    public function isSingleSourceMode()
    {
        $isSingleStoreMode = true;

        if (version_compare($this->getMagento2Version(), '2.3.0', '>=')) {
            $isSingleStoreMode = $this->_objectManager
                ->create(\Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface::class)
                ->execute();
        }

        return $isSingleStoreMode;
    }

    /**
     * @param mixed $store
     * @return string|null
     */
    public function getMassActionShipmentComment($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/mass/shipment_comment', $store);
    }

    /**
     * @param mixed $store
     * @return string|null
     */
    public function getMassActionInvoiceComment($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/mass/invoice_comment', $store);
    }

    /**
     * @param mixed $store
     * @return string|null
     */
    public function getMassActionCaptureAmount($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/mass/invoice_capture', $store);
    }

    /**
     * @param null $store
     * @return null|string
     */
    public function isMassActionShipmentEmailEnabled($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/mass/append_shipment_comment_to_email', $store);
    }

    /**
     * @param null $store
     * @return null|string
     */
    public function isMassActionInvoiceEmailEnabled($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/mass/append_invoice_comment_to_email', $store);
    }
}
