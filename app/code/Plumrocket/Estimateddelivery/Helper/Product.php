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
 * @package     Plumrocket_Estimateddelivery
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Estimateddelivery\Helper;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Plumrocket\Estimateddelivery\Helper\Data;
use Plumrocket\Estimateddelivery\Model\ProductCategory;
use Magento\Catalog\Model\Product\Type;

class Product extends Main
{
    const SECTION_ID = 'estimateddelivery';

    /**
     * @var array | null
     */
    protected $_sourceData;

    /**
     * @var \Plumrocket\Estimateddelivery\Helper\Data
     */
    protected $_helper;

    /**
     * @var ProductCategory
     */
    protected $_productCategoryModel;

    /**
     * @var ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Product constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Context $context
     * @param \Plumrocket\Estimateddelivery\Helper\Data $helper
     * @param ProductCategory $productCategoryModel
     * @param ResolverInterface $localeResolver
     * @param TimezoneInterface $localeDate
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Context $context,
        Data $helper,
        ProductCategory $productCategoryModel,
        ResolverInterface $localeResolver,
        TimezoneInterface $localeDate,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->_helper = $helper;
        $this->_productCategoryModel = $productCategoryModel;
        $this->_localeResolver = $localeResolver;
        $this->_localeDate = $localeDate;
        $this->filterProvider = $filterProvider;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;

        parent::__construct($objectManager, $context);
    }

    public function isEnabled()
    {
        return $this->_helper->moduleEnabled();
    }

    public function setCategory($category)
    {
        if (!is_object($category) && is_numeric($category)) {
            $category = $this->categoryRepository->get($category);
        }
        $this->_productCategoryModel->setCategory($category);
        return $this;
    }

    public function setProduct($product, $orderItem = null)
    {
        $this->reset();
        try {
            if (!is_object($product) && is_numeric($product)) {
                $product = $this->productRepository->getById($product);
            }
            $this->_productCategoryModel->setProduct($product, $orderItem);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
        }

        return $this;
    }

    public function reset()
    {
        $this->_productCategoryModel->reset();
        $this->_sourceData = null;
        return $this;
    }

    public function getProduct()
    {
        return $this->_productCategoryModel->getProduct();
    }

    public function getCategory()
    {
        return $this->_productCategoryModel->getCategory();
    }

    public function setSourceData($data)
    {
        $this->_productCategoryModel->reset();

        foreach ($data as $type => &$item) {
            $item = $this->_productCategoryModel->formatDates($item, $type);
        }
        unset($item);
        $this->_sourceData = $data;
        return $this;
    }

    public function getSourceData($categoryProductPages = false)
    {
        if (null === $this->_sourceData || $categoryProductPages == true) {
            $this->_sourceData = $this->_productCategoryModel->getSourceData($categoryProductPages);
        }

        return $this->_sourceData;
    }

    // protected  ---------

    protected function _param($type, $param, $default = false)
    {
        $_sourceData = $this->getSourceData();
        return isset($_sourceData[$type][$param])
            ? $_sourceData[$type][$param]
            : $default;
    }

    protected function _hasDate($type)
    {
        return $this->isEnabled()
            && ($this->_param($type, 'from') || $this->_param($type, 'text'));
    }

    public function hasDeliveryDate()
    {
        return $this->_hasDate('delivery');
    }

    public function hasShippingDate()
    {
        return $this->_hasDate('shipping');
    }

    protected function _formatDate($type)
    {
        return ($this->isEnabled()
            && $this->_hasDate($type)
            && $this->_param($type, 'from')) ? 'date': 'text';
    }

    public function formatDeliveryDate()
    {
        return $this->_formatDate('delivery');
    }

    public function formatShippingDate()
    {
        return $this->_formatDate('shipping');
    }

    protected function _getTime($type, $dir)
    {
        if ($this->isEnabled()) {
            return $this->_param($type, $dir);
        }
        return 0;
    }

    public function getDeliveryFromTime()
    {
        return $this->_getTime('delivery', 'from');
    }

    public function getShippingFromTime()
    {
        return $this->_getTime('shipping', 'from');
    }

    public function getDeliveryToTime()
    {
        return $this->_getTime('delivery', 'to');
    }

    public function getShippingToTime()
    {
        return $this->_getTime('shipping', 'to');
    }

    public function getDeliveryTime()
    {
        return $this->getDeliveryFromTime();
    }

    public function getShippingTime()
    {
        return $this->getShippingFromTime();
    }

    protected function _getDate($type, $dir)
    {
        if ($this->isEnabled()) {
            return $this->_param($type, $dir);
        }
        return '';
    }

    public function getDeliveryFromDate()
    {
        return $this->_getDate('delivery', 'from');
    }

    public function getShippingFromDate()
    {
        return $this->_getDate('shipping', 'from');
    }

    public function getDeliveryToDate()
    {
        return $this->_getDate('delivery', 'to');
    }

    public function getShippingToDate()
    {
        return $this->_getDate('shipping', 'to');
    }

    public function getDeliveryDate()
    {
        return $this->getDeliveryFromDate();
    }

    public function getShippingDate()
    {
        return $this->getShippingFromDate();
    }

    protected function _getText($type)
    {
        if ($this->isEnabled()) {
            $process = $this->filterProvider->getPageFilter();
            return $process->filter($this->_param($type, 'text'));
        }
        return '';
    }

    public function getDeliveryText()
    {
        return base64_decode($this->_getText('delivery'));
    }

    public function getShippingText()
    {
        return base64_decode($this->_getText('shipping'));
    }

    public function specialFormatDate($time)
    {
        $pattern = trim($this->getConfig(Data::SECTION_ID . '/general/date_format'));
        if (mb_strlen($pattern) < 3) {
            $pattern = null;
        }

        $date = $this->_localeDate->convertConfigTimeToUtc($time);

        return $this->_localeDate->formatDateTime(
            $date,
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
            null,
            null,
            $pattern
        );
    }

    /* deprecated function do not delete */
    public function getEstimatedDate()
    {
        return $this->getDeliveryTime();
    }

    public function getEstimatedText()
    {
        return $this->getDeliveryText();
    }

    public function getShppingFromTime()
    {
        return $this->getShippingFromTime();
    }

    public function getShppingToTime()
    {
        return $this->getShippingToTime();
    }

    public function getDelivery()
    {
        $value = '';
        if ($this->hasDeliveryDate()) {
            if ($this->formatDeliveryDate() == 'date') {
                $value = $this->specialFormatDate($this->getDeliveryFromTime());

                if ($this->getDeliveryToTime() && ($this->getDeliveryToTime() != $this->getDeliveryFromTime())) {
                     $value .= ' - ' . $this->specialFormatDate($this->getDeliveryToTime());
                }
            } else {
                $value = $this->getDeliveryText();
            }
        }

        if ($value) {
            return [
                'label' => __('Estimated Delivery Date'),
                'value' => $value,
                'custom_view' => true,
            ];
        }
    }

    public function getShipping()
    {
        $value = '';
        if ($this->hasShippingDate()) {
            if ($this->formatShippingDate() == 'date') {
                $value = $this->specialFormatDate($this->getShippingFromTime());
                if ($this->getShippingToTime() && ($this->getShippingToTime() != $this->getShippingFromTime())) {
                     $value .= ' - ' . $this->specialFormatDate($this->getShippingToTime());
                }
            } else {
                $value = $this->getShippingText();
            }
        }

        if ($value) {
            return [
                'label' => __('Estimated Shipping Date'),
                'value' => $value,
                'custom_view' => true,
            ];
        }
    }

    public function outputDeliveryDate()
    {
        if ($this->formatDeliveryDate() == 'date') {
            $result = $this->specialFormatDate($this->getDeliveryFromTime());
            if ($this->getDeliveryToTime() && ($this->getDeliveryToTime() != $this->getDeliveryFromTime())) {
                $result .= ' - ' . $this->specialFormatDate($this->getDeliveryToTime());
            }
        } else {
            $result = $this->getDeliveryText();
        }

        return $result;
    }

    public function outputShippingDate()
    {
        if ($this->formatShippingDate() == 'date') {
            $result = $this->specialFormatDate($this->getShippingFromTime());
            if ($this->getShippingToTime() && ($this->getShippingToTime() != $this->getShippingFromTime())) {
                $result .= ' - ' . $this->specialFormatDate($this->getShippingToTime());
            }
        } else {
            $result = $this->getShippingText();
        }

        return $result;
    }

    public function getOptions($item, $forOrder)
    {
        $options = [];

        if ($item->getProductType() == Configurable::TYPE_CODE) {
            $quoteItem = $item->getOptionByCode('simple_product');
            $product = $quoteItem ? $quoteItem->getProduct() : $item->getProduct();
        } else {
            $product = $item->getProduct();
        }

        $estimateddelivery = $this->setProduct($product, $forOrder ? $item : null);
        $productTypeId = $product->getTypeId();

        if ($this->isEstimatedShippingEnable($product)) {
            if ($shipping = $estimateddelivery->getShipping()) {
                $options['shipping'] = $shipping;
            } elseif ($productTypeId == Type::TYPE_SIMPLE
                && $shipping = $this->getParentShippingDelivery(false, $forOrder, $item)
            ) {
                $options['shipping'] = $shipping;
            }
        }

        if ($this->isEstimatedDeliveryEnable($product)) {
            if ($delivery = $estimateddelivery->getDelivery()) {
                $options['delivery'] = $delivery;
            } elseif ($productTypeId === Type::TYPE_SIMPLE
                && $delivery = $this->getParentShippingDelivery(true, $forOrder, $item)
            ) {
                $options['delivery'] = $delivery;
            }
        }

        return $options;
    }

    public function getParentShippingDelivery($returnDelivery, $forOrder, $item)
    {
        $this->setProduct($item->getProduct(), $forOrder ? $item : null);
        return $returnDelivery ? $this->getDelivery() : $this->getShipping();
    }

    protected function isEstimatedDeliveryEnable($product)
    {
        return $product && (int) $product->getData('estimated_delivery_enable') !== ProductCategory::DISABLED;
    }

    protected function isEstimatedShippingEnable($product)
    {
        return $product && (int) $product->getData('estimated_shipping_enable') !== ProductCategory::DISABLED;
    }

    /**
     * @deprecated function is never used
     */
    public function getBankdayDays()
    {
        return $this->_productCategoryModel->getBankDays();
    }

    public function getCurrentLocaleDate()
    {
        return $this->_localeDate->date()->format('Y-m-d H:i:s');
    }
}
