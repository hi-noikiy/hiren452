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
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Estimateddelivery\Model;

use Magento\Framework\Registry;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ProductCategory
{
    const INHERITED = 0;
    const DISABLED = 1;
    const DYNAMIC_DATE = 2;
    const DYNAMIC_RANGE = 3;
    const STATIC_DATE = 4;
    const STATIC_RANGE = 5;
    const TEXT = 6;

    protected $_result = [];
    protected $_dateEnd = '';

    protected $_helper;
    protected $_bankday;
    protected $_productModel;
    protected $_categoryModel;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|null
     */
    protected $timezone;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $configurableType;

    protected $_product;
    protected $_orderItem;
    protected $_category;
    protected $bankday;
    protected $bankdayRange;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\CategorFactory
     */
    protected $categoryFactory;

    /**
     * @var array
     */
    private $categoryCache = ['delivery' => [], 'shipping' => []];

    /**
     * ProductCategory constructor.
     *
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableType
     * @param \Plumrocket\Estimateddelivery\Helper\Data $helper
     * @param \Plumrocket\Estimateddelivery\Helper\Bankday $bankday
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param Registry $_registry
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param ProductFactory $productFactory
     * @param \Magento\Catalog\Model\CategorFactory $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableType,
        \Plumrocket\Estimateddelivery\Helper\Data $helper,
        \Plumrocket\Estimateddelivery\Helper\Bankday $bankday,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Registry $_registry,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
         $this->configurableType = $configurableType;
        $this->_helper = $helper;
        $this->_bankday = $bankday;
        $this->timezone = $timezone;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->_registry = $_registry;
        $this->productRepository = $productRepository;
        $this->_productModel = $productFactory;
        $this->_categoryModel = $categoryFactory;
    }

    public function getProduct()
    {
        if (null === $this->_product) {
            $this->_product = $this->_registry->registry('product');

            if (!$this->_product || !$this->_product->getId()) {
                $this->_product = $this->_productModel->create();
            }
        }

        return $this->_product;
    }

    public function getCategory()
    {
        if (null === $this->_category) {
            $this->_category = $this->_registry->registry('current_category');

            if (!$this->_category || !$this->_category->getId()) {
                $this->_category = $this->_categoryModel->create();
            }
        }

        return $this->_category;
    }

    public function setProduct($product, $orderItem = null)
    {
        $this->reset();

        if (null !== $orderItem) {
            $this->_orderItem = $orderItem;
        }

        $this->_product = $product;
    }

    public function setCategory($category)
    {
        $this->reset();
        $this->_category = $category;
    }

    public function reset()
    {
        $this->_result = [];
        $this->_dateEnd = '';
        $this->_category = null;
        $this->_product = null;
        $this->_orderItem = null;
    }

    public function getSourceData($categoryProductPages = false)
    {
        if ((!$this->_result || $categoryProductPages == true) && $this->_helper->moduleEnabled()) {
            $this->_result = [
                'delivery' => $this->_getData('delivery', $categoryProductPages),
                'shipping' => $this->_getData('shipping', $categoryProductPages)
            ];
        }

        return $this->_result;
    }

    /**
     * @param      $value
     * @param      $type
     * @param null $start date in store (Magento) time zone
     * @param bool $isRange
     * @return string
     */
    public function formatDate($value, $type, $start = null, $isRange = false)
    {
        if (null === $start) {
            $start = $this->timezone->date()->getTimestamp() + $this->timezone->date()->getOffset();
        }

        $days = $this->_bankday->getEndDate($type, $start, (int)$value, null, true);

        if ($type === 'delivery') {
            $this->bankday['delivery'] = $days;
        } elseif ($type === 'shipping') {
            $this->bankday['shipping'] = $days;
        }

        if ($isRange) {
            if ($type === 'delivery') {
                $this->bankdayRange['delivery'] = $days;
            } elseif ($type === 'shipping') {
                $this->bankdayRange['shipping'] = $days;
            }
        }

        return strftime(
            '%Y-%m-%d %H:%M:%S',
            $this->_bankday->getEndDate($type, $start, (int)$value)
        );
    }

    public function getBankDays($type = false)
    {
        if ($type && isset($this->bankday[$type])) {
            return $this->bankday[$type];
        }
        return $this->bankday;
    }

    public function getBankDaysRange($type = false)
    {
        if ($type && isset($this->bankdayRange[$type])) {
            return $this->bankdayRange[$type];
        }
        return $this->bankdayRange;
    }

    /**
     * @param      $data
     * @param      $type
     * @param null $start date in store (Magento) time zone
     * @return mixed
     */
    public function formatDates($data, $type, $start = null)
    {
        if (! isset($data['enable'])) {
            return $data;
        }

        switch ($data['enable']) {
            case self::DYNAMIC_RANGE:
                $data['to'] = $this->formatDate($data['to_origin'], $type, $start, true);
                // no break
            case self::DYNAMIC_DATE:
                $data['from'] = $this->formatDate($data['from_origin'], $type, $start);
                break;
        }

        return $data;
    }

    protected function _getData($type, $categoryProductPages = false)
    {
        $result = [];
        $product = $this->getProduct();

        if ($product && $product->getId()) {
            if ($categoryProductPages) {
                $result[$product->getId()] = $this->_getDataFromProduct($product, $type);
                $result[$product->getId() . "_bank"] = $this->getBankDays($type);
                $result[$product->getId() . "_bank_range"] = $this->getBankDaysRange($type);

                if ($product->getTypeId() === Configurable::TYPE_CODE) {
                    foreach ($product->getTypeInstance()->getUsedProducts($product) as $simpleproduct) {
                        $simpleProductResult = $this->_getDataFromProduct(
                            $simpleproduct,
                            $type,
                            $product
                        );

                        if (! $simpleProductResult || $result[$product->getId()] != $simpleProductResult) {
                            $result[$simpleproduct->getId()] = $simpleProductResult;
                            $result[$simpleproduct->getId() . "_bank"] = $this->getBankDays($type);
                            $result[$simpleproduct->getId() . "_bank_range"] = $this->getBankDaysRange($type);
                        }
                    }
                }
            } else {
                $result = $this->_getDataFromProduct($product, $type);
            }
        } else {
            $category = $this->getCategory();

            if ($categoryProductPages) {
                $result[$category->getId()] = $this->_getDataFromCategory($category, $type);
                $result[$category->getId() . "_bank"] = $this->getBankDays($type);
                $result[$category->getId() . "_bank_range"] = $this->getBankDaysRange($type);
            } else {
                $result = $this->_getDataFromCategory($category, $type);
            }

        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $type
     * @param bool $parentPoduct
     * @return array|bool|mixed|void|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getDataFromProduct($product, $type, $parentPoduct = false)
    {
        $enableType = (int) $this->_value($product, $type, 'enable');

        if ($enableType !== self::INHERITED) {
            return $this->_parseData($product, $type);
        }

        if ($parentPoduct) {
             return;
        }

        if ($product->getTypeId() === Type::TYPE_SIMPLE
            && $parentPoductId = $this->configurableType->getParentIdsByChild($product->getId())
        ) {
            if (isset($parentPoductId[0])) {
                $product = $this->productRepository->getById($parentPoductId[0]);
                return $this->_getDataFromProduct(
                    $product,
                    $type
                );
            }
        }

        $categoryItems = $product->getCategory()
            ? [$product->getCategory()]
            : $this->addEstimatedDeliveryAttributesToSelect($product->getCategoryCollection());

        foreach ($categoryItems as $category) {
            $result = $this->_getDataFromCategory($category, $type);

            if ($result) {
                return $result;
            }
        }
    }

    protected function _getDataFromCategory(CategoryInterface $category, $type)
    {
        if (! isset($this->categoryCache[$type][$category->getId()])) {
            if ((int) $this->_value($category, $type, 'enable') !== self::INHERITED) {
                return $this->categoryCache[$type][$category->getId()] = $this->_parseData($category, $type);
            }

            if (isset($this->categoryCache[$type][$category->getParentId()])) {
                return $this->categoryCache[$type][$category->getId()] = $this->categoryCache[$category->getParentId()];
            }

            $parentCategories = $this->getParentCategories($category);
            $this->categoryCache[$type][$category->getId()] = self::INHERITED;

            foreach ($parentCategories as $parentCategory) {
                $this->categoryCache[$type][$parentCategory->getId()] = &$this->categoryCache[$category->getId()];

                if ((int) $this->_value($parentCategory, $type, 'enable') !== self::INHERITED) {
                    $this->categoryCache[$type][$category->getId()] = $this->_parseData($parentCategory, $type);
                    break;
                }
            }
        }

        return $this->categoryCache[$type][$category->getId()];
    }

    private function useDefaultText($type, $result, $field)
    {
        if (false !== $result && is_array($result) && $this->_helper->isEnabledDefaultText($type)) {
            $result['text'] = trim($this->_helper->getDefaultText($type));
            $result['text'] = base64_encode($result['text']);

            if ($field !== 'text') {
                $result['enable'] = (string)self::TEXT;
                unset($result[$field]);
            }
        }

        return $result;
    }

    protected function _parseData($object, $type, $originalValue = false)
    {
        $enable = $this->_value($object, $type, 'enable');
        $result = ['from' => '', 'to' => '', 'text' => '', 'enable' => $enable];
        $today = $this->timezone->date()->setTime(0, 0)->getTimestamp();
        $start = null;

        if ($this->_orderItem && $this->_orderItem->getId()) {
            $start = strtotime($this->timezone->formatDateTime(
                $this->_orderItem->getCreatedAt(),
                \IntlDateFormatter::SHORT,
                \IntlDateFormatter::SHORT,
                'en_US'
            ));
        }

        switch ($enable) {
            case self::DYNAMIC_RANGE:
                $result['to_origin'] = $this->_value($object, $type, 'days_to');

                if (is_null($result['to_origin'])) {
                    $result = $this->useDefaultText($type, $result, 'to_origin');
                    break;
                }
                // no break
            case self::DYNAMIC_DATE:
                $result['from_origin'] = $this->_value($object, $type, 'days_from');

                if (is_null($result['from_origin'])) {
                    $result = $this->useDefaultText($type, $result, 'from_origin');
                }
                break;

            case self::STATIC_RANGE:
                $dateTo = $this->_value($object, $type, 'date_to');
                if (strtotime($dateTo) >= $today) {
                    $result['to'] = $this->_helper->formattingDate($originalValue, $dateTo);
                    $dateFrom = $this->_value($object, $type, 'date_from');
                    $result['from'] = $this->_helper->formattingDate($originalValue, $dateFrom);
                    if (is_null($result['to'])) {
                        $result = $this->useDefaultText($type, $result, 'from');
                        break;
                    }
                    if (is_null($result['to'])) {
                        $result = $this->useDefaultText($type, $result, 'to');
                    }
                    break;
                } else {
                    $result = null;
                }
                // no break
            case self::STATIC_DATE:
                $dateFrom = $this->_value($object, $type, 'date_from');
                if (strtotime($dateFrom) >= $today) {
                    $result['from'] = $this->_helper->formattingDate($originalValue, $dateFrom);
                    if (is_null($result['from'])) {
                        $result = $this->useDefaultText($type, $result, 'from');
                    }
                } else {
                    $result = null;
                }
                break;

            case self::TEXT:
                $result['text'] = base64_encode($this->_value($object, $type, 'text'));
                if (empty($result['text'])) {
                    $result = $this->useDefaultText($type, $result, 'text');
                }
                break;

            case self::DISABLED:
                $result = false;
                break;
            default:
                $result = null;
                break;
        }

        if (!$originalValue) {
            return $this->formatDates($result, $type, $start);
        }

        return $result;
    }

    protected function _value($object, $type, $param)
    {
        return $object->getData($this->_param($type, $param));
    }

    protected function _param($type, $param)
    {
        return 'estimated_' . $type . '_' . $param;
    }

    /**
     * @param CategoryInterface $category
     * @return CategoryInterface[]|\Magento\Framework\DataObject[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getParentCategories(CategoryInterface $category)
    {
        $categories = $this->categoryCollectionFactory->create();
        $categories->setStore(
            $this->storeManager->getStore()
        )->addFieldToFilter(
            'entity_id',
            ['in' => $category->getParentIds()]
        )->addFieldToFilter(
            'is_active',
            1
        )->setOrder('level', 'DESC');

        $this->addEstimatedDeliveryAttributesToSelect($categories);

        return $categories->getItems();
    }

    /**
     * @param $collection
     * @return \Magento\Framework\Data\Collection
     */
    private function addEstimatedDeliveryAttributesToSelect($collection)
    {
        return $collection->addAttributeToSelect([
            'estimated_shipping_text',
            'estimated_shipping_date_to',
            'estimated_shipping_date_from',
            'estimated_shipping_days_to',
            'estimated_shipping_days_from',
            'estimated_shipping_enable',
            'estimated_delivery_text',
            'estimated_delivery_date_to',
            'estimated_delivery_date_from',
            'estimated_delivery_days_to',
            'estimated_delivery_days_from',
            'estimated_delivery_enable'
        ], 'left');
    }
}
