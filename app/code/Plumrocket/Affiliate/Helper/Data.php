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
 * @package     Plumrocket Affiliate v2.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Helper;

class Data extends Main
{
    /**
     * needed for Plumrocket Base and for function "getConfigPath"
     * @var string
     */
    protected $_configSectionId = 'praffiliate';

    /**
     * @var string
     */
    public static $configSectionId = 'praffiliate';

    /**
     * @var string
     */
    public static $routeName = 'praffiliate';

    /**
     * @var array
     */
    protected $_affiliates;

    /**
     * @var array
     */
    protected $_includeons;

    /**
     * @var Plumrocket\Affiliate\Model\Type\ResoutceModel\Type\Collection
     */
    protected $_types;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var \Magento\Config\Model\Config
     */
    protected $config;
    /**
     * @var \Plumrocket\Affiliate\Model\AffiliateManager
     */
    protected $affiliateManager;
    /**
     * @var \Plumrocket\Affiliate\Model\AffiliateFactory
     */
    protected $affiliateFactory;
    /**
     * @var \Plumrocket\Affiliate\Model\TypeFactory
     */
    protected $typeFactory;
    /**
     * @var \Plumrocket\Affiliate\Model\IncludeonFactory
     */
    protected $includeonFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context        $context
     * @param \Magento\Framework\ObjectManagerInterface    $objectManager,
     * @param \Magento\Config\Model\Config                 $config
     * @param \Magento\Framework\App\ResourceConnection    $resourceConnection
     * @param \Plumrocket\Affiliate\Model\AffiliateManager $affiliateManager
     * @param \Plumrocket\Affiliate\Model\AffiliateFactory $affiliateFactory
     * @param \Plumrocket\Affiliate\Model\TypeFactory      $typeFactory
     * @param \Plumrocket\Affiliate\Model\IncludeonFactory $includeonFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface       $objectManager,
        \Magento\Framework\App\Helper\Context           $context,
        \Magento\Framework\App\ResourceConnection       $resourceConnection,
        \Magento\Config\Model\Config                    $config,
        \Plumrocket\Affiliate\Model\AffiliateManager    $affiliateManager,
        \Plumrocket\Affiliate\Model\AffiliateFactory    $affiliateFactory,
        \Plumrocket\Affiliate\Model\TypeFactory         $typeFactory,
        \Plumrocket\Affiliate\Model\IncludeonFactory    $includeonFactory
    ) {
        parent::__construct($objectManager, $context);
        $this->resourceConnection   = $resourceConnection;
        $this->config               = $config;
        $this->affiliateManager     = $affiliateManager;
        $this->affiliateFactory     = $affiliateFactory;
        $this->typeFactory          = $typeFactory;
        $this->includeonFactory     = $includeonFactory;
    }

    /**
     * Is module enabled
     * @param  int $store
     * @return boolean
     */
    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId.'/general/enabled');
    }

    /**
     * Get page affiliater
     * @return  Array
     */
    public function getPageAffiliates()
    {
        if ($this->_affiliates === null) {
            $collection = $this->affiliateFactory->create()
                ->getCollection()
                ->addEnabledStatusToFilter()
                ->addStoreToFilter();

            $this->_affiliates = [];
            $types = $this->getAffiliateTypes();
            foreach ($collection as $item) {
                $this->_affiliates[] = $item->setTypes($types)->getTypedModel();
            }
        }

        return $this->_affiliates;
    }

    /**
     * @return Plumrocket\Affiliate\Model\Type\ResoutceModel\Type\Collection
     */
    public function getAffiliateTypes()
    {
        if ($this->_types === null) {
            $this->_types = $this->typeFactory->create()
                ->getCollection()
                ->setOrder('main_table.order', 'ASC');
        }
        return $this->_types;
    }

    /**
     * Disable extension
     * @return void
     */
    public function disableExtension()
    {
        $connection = $this->resourceConnection->getConnection('core_write');
        $connection->delete($this->resourceConnection->getTableName('core_config_data'),
            [$connection->quoteInto('path = ?', $this->_configSectionId . '/general/enabled')]
        );

        $this->config->setDataByPath($this->_configSectionId . '/general/enabled', 0);
        $this->config->save();
    }

    /**
     * Get includeon
     * @param  int $id
     * @return Plumrocket\Affiliate\Model\Includeon | null
     */
    public function getIncludeon($id)
    {
        if ($this->_includeons === null) {
            $this->getIncludeonCollection();
        }

        if (isset($this->_includeons[$id])) {
            return $this->_includeons[$id];
        }

        return null;
    }

    /**
     * Get includeon collection
     * @return Array
     */
    public function getIncludeonCollection()
    {
        if ($this->_includeons === null) {
            $collection = $this->includeonFactory->create()
                ->getCollection();

            foreach ($collection as $item) {
                $this->_includeons[$item->getId()] = $item;
            }
        }

        return $this->_includeons;
    }
}
