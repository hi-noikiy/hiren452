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
 * @package     Plumrocket Ajaxcart v2.x.x
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Helper;

use Plumrocket\Ajaxcart\Model\System\Config\Source\MinicartStyle;
use Plumrocket\Ajaxcart\Model\System\Config\Source\Modes as WorkModes;

/**
 * Class Data
 *
 * @package Plumrocket\Ajaxcart\Helper
 */
class Data extends Main
{
    /**
     * @vat string
     */
    protected $_configSectionId = 'prajaxcart';

    /**
     * @var array
     */
    protected $addedQuoteItems = [];

    /**
     * @var \Magento\Config\Model\Config $config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    protected $resourceConnection;

    /**
     * @var array
     */
    protected $disabledPluginRoutes = [
        'checkout_cart_index',
        'checkout_cart_configure',
        'review_product_listAjax',
    ];

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface         $objectManager
     * @param \Magento\Framework\App\Helper\Context             $context
     * @param \Magento\Config\Model\Config                      $config
     * @param \Magento\Framework\App\ResourceConnection         $resourceConnection
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($objectManager, $context);
        $this->priceCurrency = $priceCurrency;
    }

    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId . '/general/enabled', $store);
    }

    public function getAddedQuoteItems()
    {
        return $this->addedQuoteItems;
    }

    public function disableExtension()
    {
        $resource = $this->resourceConnection;
        $connection = $resource->getConnection('core_write');
        $connection->delete(
            $resource->getTableName('core_config_data'),
            [
                $connection->quoteInto('path = ?', $this->_configSectionId  . '/general/enabled')
            ]
        );
        $this->config->setDataByPath($this->_configSectionId  . '/general/enabled', 0);
        $this->config->save();
    }

    public function showRelatedOnEdit()
    {
        return (int)$this->getConfig($this->_configSectionId . '/general/related_on_edit');
    }

    public function checkoutBtnAction()
    {
        return (int)$this->getConfig($this->_configSectionId . '/general/checkout_btn_action');
    }

    public function continueShoppingLink()
    {
        return (int)$this->getConfig($this->_configSectionId . '/general/continue_shoping_link');
    }

    public function continueCustomLink()
    {
        return (string)$this->getConfig($this->_configSectionId . '/general/continue_custom_link');
    }

    /**
     * @return int
     */
    public function getWorkMode()
    {
       return (int)$this->getConfig($this->_configSectionId . '/additional_configuration/mode');
    }

    /**
     * Get Add To Cart Button Selector
     *
     * @return string
     */
    public function getAtcBtnSelector()
    {
        return (string)$this->getConfig($this->_configSectionId . '/additional_configuration/button_selector');
    }

    public function newAddedQuoteItems($quoteItems)
    {
        $this->addedQuoteItems = $quoteItems;
        return $this;
    }

    public function preventAjaxacartJs()
    {
        return $this->_request->getModuleName() == 'prajaxcart'
            || $this->isAmpIframe()
            || in_array($this->_request->getFullActionName(), $this->disabledPluginRoutes);
    }

    public function isAjaxcartRequest()
    {
        return $this->moduleEnabled()
        && $this->_request->getFullActionName() === 'prajaxcart_cart_addconfigure';
    }

    public function isTargetRuleModule()
    {
        return $this->_moduleManager->isEnabled('Magento_TargetRule');
    }

    private function isAmpIframe()
    {
        return $this->moduleAmpEnabled()
            && '1' === $this->_request->getParam('only-options');
    }

    private function moduleAmpEnabled()
    {
        return 2 === $this->moduleExists('Amp');
    }

    /**
     * @param      $amount
     * @param bool $includeContainer
     * @return float
     */
    public function formatPrice($amount, $includeContainer = false)
    {
        return $this->priceCurrency->format($amount, $includeContainer);
    }

    public function showProductQtyCartOnProductList()
    {
        return (int)$this->getConfig($this->_configSectionId . '/general/qty_on_product_list');
    }

    public function getQtyBlockClass()
    {
        $className = $this->getConfig($this->_configSectionId . '/general/product_qty_selector');
        return (null != $className) ? $className : 'product-item-info';
    }

    public function getMinicartTemplate()
    {
        $minicartStyle = $this->_getMinicartStyle();

        if ($this->moduleEnabled() && MinicartStyle::RIGHT_BAR === $minicartStyle) {
            $result = 'Plumrocket_Ajaxcart::cart/minicart.phtml';
        } else {
            $result = 'Magento_Checkout::cart/minicart.phtml';
        }

        return $result;
    }

    public function getMinicartItemRenderer()
    {
        $minicartStyle = $this->_getMinicartStyle();

        if ($this->moduleEnabled() && MinicartStyle::RIGHT_BAR === $minicartStyle) {
            $result = 'Plumrocket_Ajaxcart/minicart/item/default';
        } else {
            $result = 'Magento_Checkout/minicart/item/default';
        }

        return $result;
    }

    protected function _getMinicartStyle()
    {
        return $this->getConfig($this->_configSectionId . '/general/minicart_style');
    }

    /**
     * @return bool
     */
    public function isManualMode()
    {
        return $this->getWorkMode() === WorkModes::MANUAL;
    }

    /**
     * @return bool
     */
    public function isAutomaticMode()
    {
        return $this->getWorkMode() === WorkModes::AUTOMATIC;
    }
}
