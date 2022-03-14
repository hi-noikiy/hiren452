<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Search Autocomplete & Suggest
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Search\Helper;

class Data extends Main
{
    /**
     * @var string
     */
    private $words;

    /**
     * Configuration path to enable module
     */
    const MODULE_ENABLED_PATH = 'general/enabled';

    /**
     * Like Separator
     */
    const LIKE_SEPARATOR = 'search/like_separator';

    /**
     * Config section id
     *
     * @var string
     */
    protected $_configSectionId = 'prsearch';

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Config\Model\Config
     */
    private $config;

    /**
     * @var \Plumrocket\Search\Model\System\Config\Source\Categories
     */
    private $categories;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    private $helperCatalogSearch;

    /**
     * @var \Magento\Framework\Filter\Factory
     */
    private $filterFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var null
     */
    private $versionCompare = null;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\ProductMetadataInterface          $productMetadata
     * @param \Magento\Framework\ObjectManagerInterface                $objectManager
     * @param \Magento\Framework\App\Helper\Context                    $context
     * @param \Magento\Config\Model\Config                             $config
     * @param \Magento\Framework\App\ResourceConnection                $resourceConnection
     * @param \Plumrocket\Search\Model\System\Config\Source\Categories $categories
     * @param \Magento\Store\Model\StoreManagerInterface               $storeManager
     * @param \Magento\Framework\App\Request\Http                      $request
     * @param \Magento\CatalogSearch\Helper\Data                       $helperCatalogSearch
     * @param \Magento\Framework\Filter\Factory                        $filterFactory
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Plumrocket\Search\Model\System\Config\Source\Categories $categories,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\CatalogSearch\Helper\Data $helperCatalogSearch,
        \Magento\Framework\Filter\Factory $filterFactory
    ) {
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
        $this->categories = $categories;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->helperCatalogSearch = $helperCatalogSearch;
        $this->filterFactory = $filterFactory;
        $this->productMetadata = $productMetadata;

        parent::__construct($objectManager, $context);
    }

    /**
     * @return string
     */
    public function getQueryText()
    {
        return $this->helperCatalogSearch->getEscapedQueryText();
    }

    /**
     * @param null $default
     * @return int|null
     */
    public function getQueryCategory($default = null)
    {
        return (int)$this->request->getParam('cat');
    }

    /**
     * @param null $queryText
     * @return \Zend_Filter_Interface
     */
    public function getQueryWords($queryText = null)
    {
        if (null === $queryText) {
            $queryText = $this->getQueryText();
        }

        return $this->filterFactory->createFilter('splitWords', [$queryText, true]);
    }

    /**
     * @param null $queryText
     * @param null $categoryId
     * @return string
     */
    public function getResultUrl($queryText = null, $categoryId = null)
    {
        if (null === $queryText) {
            $queryText = $this->getQueryText();
        }

        if (null === $categoryId) {
            $categoryId = $this->getQueryCategory();
        }

        return $this->_getUrl(
            'catalogsearch/result',
            [
                '_query' => [
                    $this->helperCatalogSearch->getQueryParamName() => $queryText,
                    'cat' => $categoryId
                ],

                '_secure' => $this->request->isSecure()
            ]
        );
    }

    /**
     * @param null $store
     * @return bool
     */
    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId . '/' . self::MODULE_ENABLED_PATH, $store);
    }

    /**
     * @param null $store
     * @return bool
     */
    public function likeSeparator($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/' . self::LIKE_SEPARATOR, $store);
    }

    /**
     * @return array
     */
    public function getCategoryTree()
    {
        $categories = $this->categories
            ->setSkip(true)
            ->setDepthStr('&nbsp;&nbsp;')
            ->toArray();

        if ($categories) {
            $categories = [0 => 'All'] + $categories;
        }

        return $categories;
    }

    /**
     * Disable Extension
     */
    public function disableExtension()
    {
        $resource = $this->resourceConnection;
        $connection = $resource->getConnection('core_write');

        $connection->delete(
            $resource->getTableName('core_config_data'),
            [
                $connection->quoteInto('path = ?', $this->_configSectionId  . '/' . self::MODULE_ENABLED_PATH)
            ]
        );

        $this->config->setDataByPath($this->_configSectionId  . '/' . self::MODULE_ENABLED_PATH, 0);
        $this->config->save();
    }

    /**
     * @return string
     */
    public function getSuggestUrl()
    {
        return $this->_getUrl(
            'prsearch/ajax/index',
            ['_secure' => $this->_getRequest()->isSecure()]
        );
    }

    /**
     * @return mixed
     */
    public function isCurrentlySecure()
    {
        return $this->storeManager->getStore()->isCurrentlySecure();
    }

    /**
     * @param null $str
     * @return mixed
     */
    public function splitWords($str = null)
    {
        if (null === $this->words) {
            if (null === $str) {
                $str = $this->getQueryText();
            }

            $maxQueryWords = $this->getConfig('catalog/search/max_query_length');
            $filter = $this->filterFactory->createFilter('splitWords', [true, $maxQueryWords]);

            $this->words = $filter->filter($str);
        }

        return $this->words;
    }

    /**
     * @return bool|null
     */
    public function versionCompare()
    {
        if ($this->versionCompare !== null) {
            return $this->versionCompare;
        }

        return $this->versionCompare = (version_compare($this->productMetadata->getVersion(), '2.2.4') >= 0);
    }
}
