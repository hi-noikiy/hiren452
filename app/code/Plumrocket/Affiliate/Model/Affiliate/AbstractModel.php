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
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

abstract class AbstractModel extends \Magento\Framework\Model\AbstractModel
{
    const ENABLED_STATUS        = 'ENABLED';
    const DISABLED_STATUS       = 'DISABLED';

    const SECTION_HEAD          = 'head';
    const SECTION_BODYBEGIN     = 'bodybegin';
    const SECTION_BODYEND       = 'bodyend';

    /**
     * Types
     * @var Plumrocket\Affiliate\Model\ResourceModel\Type\Collection
     */
    protected $_types           = null;

    /**
     * Type
     * @var Plumrocket\Affiliate\Model\Type
     */
    protected $_type            = null;

    /**
     * Page section
     * @var string
     */
    protected $_pageSections    = null;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    protected $_cookieManager;

    /**
     * @var \Plumrocket\Affiliate\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfigInterface;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Catalog\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager            $cookieManager
     * @param \Plumrocket\Affiliate\Helper\Data                            $dataHelper
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Customer\Model\Session                              $customerSession
     * @param \Magento\Checkout\Model\Session                              $checkoutSession
     * @param \Magento\Framework\App\RequestInterface                      $request
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Catalog\Model\ProductFactory                        $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory                       $categoryFactory
     * @param \Magento\Sales\Model\OrderFactory                            $orderFactory
     * @param \Magento\Directory\Model\RegionFactory                       $regionFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress         $remoteAddress
     * @param \Magento\Catalog\Helper\Image                                $imageHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfigInterface
     * @param \Magento\Framework\Url                                       $url
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager,
        \Plumrocket\Affiliate\Helper\Data $dataHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Url $url,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_cookieManager = $cookieManager;
        $this->_customerSession = $customerSession;
        $this->_dataHelper = $dataHelper;
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_remoteAddress = $remoteAddress;
        $this->_imageHelper = $imageHelper;
        $this->_scopeConfigInterface = $scopeConfigInterface;
        $this->_url = $url;
        $this->_productFactory = $productFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_regionFactory = $regionFactory;
        $this->_orderFactory = $orderFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get last order
     * @return Magento\Sales\Model\Order
     */
    public function getLastOrder()
    {
        if (!$this->_registry->registry('current_order')) {
            $this->_registry->register('current_order', $this->_checkoutSession->getLastRealOrder());
        }
        return $this->_registry->registry('current_order');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Plumrocket\Affiliate\Model\ResourceModel\Affiliate::class);
    }

    /**
     * Simulate Load
     * @param  \Plumrocket\Affiliate\Model\Affiliate $affiliate
     * @return $this
     */
    public function simulateLoad($affiliate)
    {
        $this->setData($affiliate->getData());
        $this->setOrigData();
        $this->_hasDataChanges = false;

        $this->_types = $affiliate->getTypes();
        $this->_type = $affiliate->getType();
        return $this;
    }

    /**
     * Get types
     * @return Plumrocket\Affiliate\Model\Type
     */
    public function getTypes()
    {
        if ($this->_types === null) {
            $this->_types = $this->_dataHelper->getAffiliateTypes();
        }
        return $this->_types;
    }

    /**
     * Get type
     * @param  int $typeId
     * @return string
     */
    public function getType($typeId = null)
    {
        if ($this->_type === null) {
            if ($typeId === null) {
                $typeId = $this->getTypeId();
            }

            $types = $this->getTypes();
            foreach ($types as $type) {
                if ($type->getId() == $typeId) {
                        $this->_type  = $type;
                    break;
                }
            }
        }
        return $this->_type;
    }

    /**
     * Get merchant id
     * @return string
     */
    public function getMerchantId()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['merchant_id']) ? $additionalData['merchant_id'] : '';
    }

    /**
     * Is cps enabled
     * @return bool
     */
    public function getCpsEnabled()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['cps_enable']) ? $additionalData['cps_enable'] : '';
    }

    /**
     * Is cpl enabled
     * @return bool
     */
    public function getCplEnabled()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['cpl_enable']) ? $additionalData['cpl_enable'] : '';
    }

    /**
     * Get renderer code
     * @param  string $_section
     * @return string           html
     */
    protected function _getRenderedCode($_section, $_includeon = null)
    {
        return '';
    }

    /**
     * Get current locale code
     * @param  int $storeId
     * @return string
     */
    public function getLocaleCode($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }

        return $this->_scopeConfigInterface->getValue(
            \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_LOCALE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get page section
     * @return array
     */
    public function getPageSections()
    {
        if ($this->_pageSections === null) {
            $this->_pageSections = [
                [
                    'key'   => self::SECTION_HEAD,
                    'lable' => __('Script in &#60;HEAD&#62; section'),
                ],
                [
                    'key'   => self::SECTION_BODYBEGIN,
                    'lable' => __('Script after &#60;BODY&#62; opening tag'),
                ],
                [
                    'key'   => self::SECTION_BODYEND,
                    'lable' => __('Script before &#60;/BODY&#62; closing tag'),
                ],
            ];
        }
        return $this->_pageSections;
    }

    /**
     * Get library html
     * @param  string $_section
     * @return string
     */
    public function getLibraryHtml($_section)
    {
        $getSectionLibrary = 'getSection'.ucfirst($_section).'Library';

        if ($lib = $this->$getSectionLibrary()) {
            $mediaUrl = $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);

            return '<script type="text/javascript" src="'.$mediaUrl.$lib.'"></script>'."\n\r";
        }
        return null;
    }

    /**
     * get code html
     * @param  string $_section
     * @param  string $_includeon
     * @return string
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $getSectionCode = 'getSection'.ucfirst($_section).'Code';
        if ($code = $this->$getSectionCode()) {
            return $code."\n\r";
        }
        return null;
    }

    /**
     * Get addional data array
     * @return array
     */
    public function getAdditionalDataArray()
    {
        if (!$this->getAdditionalData()) {
            return $this->getDefaultAdditionalDataArray();
        } else {
            if (!is_array($this->getAdditionalData())) {
                return json_decode($this->getAdditionalData(), true);
            } else {
                return $this->getAdditionalData();
            }
        }
    }

    /**
     * Get addional data value
     * @return array
     */
    public function getAdditionalDataValue($key)
    {
        $additionalData = $this->getAdditionalData();
        if (!$additionalData) {
            return null;
        }
        if (!is_array($additionalData)) {
            $additionalData = json_decode($this->getAdditionalData(), true);
        }

        return (isset($additionalData[$key])) ? $additionalData[$key] : null;
    }

    /**
     * Set addtional data
     * @param array $values
     * @return  $this
     */
    public function setAdditionalDataValues($values)
    {
        $data = $this->getAdditionalDataArray();

        foreach ($values as $key => $value) {
            $data[$key] = $value;
        }

        $this->setAdditionalData(json_encode($data));
        return $this;
    }

    /**
     * get defailt addtional data array
     * @return array
     */
    public function getDefaultAdditionalDataArray()
    {
        return [];
    }

    /**
     * Set stores
     * @param array $storeArray
     * @return $this
     */
    public function setStores(array $storeArray)
    {
        if (in_array(0, $storeArray)) {
            $stores = 0;
        } else {
            $stores = ','.implode(',', $storeArray).',';
        }

        $this->setData('stores', $stores);
        return $this;
    }

    /**
     * Get stores
     * @return array
     */
    public function getStores()
    {
        if ($this->hasData('stores')) {
            if (is_array($this->getData('stores'))) {
                return $this->getData('stores');
            }
            return explode(',', $this->getData('stores'));
        }

        return [0];
    }

    /**
     * Get cuurent currency core
     * @param  \Magento\Sales\Model\Order $order
     * @return string
     */
    public function getCurrencyCode($order)
    {
        $currencyCode = null;
        $currency = $order->getOrderCurrency();
        if (is_object($currency)) {
            $currencyCode = $currency->getCurrencyCode();
        }
        return $currencyCode;
    }

    /**
     * Is new customer
     * @param  \Magento\Sales\Model\Order  $order
     * @return boolean
     */
    public function isNewCustomer($order)
    {
        $collection = $this->_orderFactory->create()->getCollection()
            ->addFieldToFilter('entity_id', ['neq' => $order->getId()])
            ->addFieldToFilter('store_id', $order->getStoreId())
            ->setPageSize(1);

        if ($order->getCustomerId()) {
            $collection->getSelect()
                ->where('
                    `customer_email` = "'. $order->getCustomerEmail() .'" 
                    OR 
                    `customer_id` = '. (int)$order->getCustomerId()
                );
        } else {
            $collection->addFieldToFilter('customer_email', $order->getCustomerEmail());
        }

        return !count($collection);
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        if (isset($this->_data['id'])) {
            return 'Edit Affiliate Program - "'. $this->_data['name'] .'"';
        }
    }
}
