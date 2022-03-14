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
 * @package     Plumrocket_Datagenerator
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Model;

class Render extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\Config\CacheInterface
     */
    protected $_cache;

    /**
     * @var \Plumrocket\Datagenerator\Helper\Data
     */
    protected $_dataHelper;

    /**
     * extension of file
     * @var string
     */
    protected $_ext;

    /**
     * replace from
     * @var string
     */
    protected $_replaceFrom;

    /**
     * replace to
     * @var string
     */
    protected $_replaceTo;

    /**
     * filters (rules) status
     * @var string
     */
    protected $enabledCondition;

    /**
     * memory limit
     * @var string
     */
    protected $_memoryLimit = '1024M';

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * category collection
     * @var array
     */
    protected $_categories;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_productCollection;

    /**
     * Enabled childs render
     * @var boolean
     */
    protected $_enabledChildsRender = false;

    /**
     * Enable product category render
     * @var boolean
     */
    protected $_enabledProductCategoryRender = false;

    /**
     * Enable site render. It s true when in code item are site tags
     * @var boolean
     */
    protected $_enabledSiteRender = false;

    /**
     * Enabled sold qty
     * @var boolean
     */
    protected $_enabledSoldQty = false;

    /**
     * Enabled product qty
     * @var boolean
     */
    protected $_enabledProductQty = false;

    /**
     * Enabled flat products
     * @var boolean
     */
    protected $_enabledFlatProducts = false;

    /**
     * Enabled product stock status
     * @var bool
     */
    protected $_enabledProductStockStatus = false;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Flat
     */
    protected $_flatProduct;

    /**
     * @var array
     */
    protected $_fields;

    /**
     * Tags
     * @var array
     */
    protected $_tags;

    /**
     * Date time
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * Product count
     * @var integer
     */
    protected $_productsCount = 0;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Model\Information
     */
    protected $_storeInformation;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\Collection
     */
    protected $_reportsProductCollection;

    /**
     * @var \Magento\Catalog\ResourceModel\Product\Attribute\Collection
     */
    protected $_productAttributeCollection;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $_stockHelper;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $_configurableType;

    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\Grouped
     */
    protected $_groupedType;

    /**
     * @var \Magento\Bundle\Model\Product\Type
     */
    protected $_bundleType;

    /**
     * @var \Magento\Bundle\Model\Product\Price
     */
    protected $_bundlePrice;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogHelper;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockState;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $_filterManager;

    /**
     * @var \Plumrocket\Datagenerator\Model\Template\Space
     */
    protected $spaceModel;

    /**
     * @var array
     */
    protected $shareASale = [
        'commission' => [
                'products' => [],
                'enabled'  => false,
        ],
        'subcategory' => [
                'products' => [],
                'enabled'  => false,
        ],
    ];

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Plumrocket\Datagenerator\Helper\Data $dataHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @param \Magento\Catalog\Model\ResourceModel\Product\Flat $flatProduct
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
     * @param \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedType
     * @param \Magento\Bundle\Model\Product\Type $bundleType
     * @param \Magento\Bundle\Model\Product\Price $bundlePrice
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockState
     * @param \Magento\CatalogInventory\Helper\Stock $stockHelper
     * @param \Magento\Store\Model\Information $storeInformation
     * @param \Magento\Catalog\Helper\ImageFactory $imageHelper
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributeCollection
     * @param \Magento\Reports\Model\ResourceModel\Product\Sold\Collection $reportsProductCollection
     * @param \Plumrocket\Datagenerator\Model\Template\Space $spaceModel
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context                                    $context,
        \Magento\Framework\Registry                                         $registry,
        \Magento\Framework\Config\CacheInterface                            $cache,
        \Magento\Framework\Filter\FilterManager                             $filterManager,
        \Plumrocket\Datagenerator\Helper\Data                               $dataHelper,
        \Magento\Catalog\Model\CategoryFactory                              $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Collection             $productCollection,
        \Magento\Catalog\Model\ResourceModel\Product\Flat                   $flatProduct,
        \Magento\Catalog\Model\ProductFactory                               $productFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime                         $dateTime,
        \Magento\Store\Model\StoreManagerInterface                          $storeManager,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable        $configurableType,
        \Magento\GroupedProduct\Model\Product\Type\Grouped                  $groupedType,
        \Magento\Bundle\Model\Product\Type                                  $bundleType,
        \Magento\Bundle\Model\Product\Price                                 $bundlePrice,
        \Magento\CatalogInventory\Api\StockStateInterface                   $stockState,
        \Magento\CatalogInventory\Helper\Stock                              $stockHelper,
        \Magento\Store\Model\Information                                    $storeInformation,
        \Magento\Catalog\Helper\ImageFactory                                $imageHelper,
        \Magento\Catalog\Helper\Data                                        $catalogHelper,
        \Magento\Catalog\Helper\Product                                     $productHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection   $productAttributeCollection,
        \Magento\Reports\Model\ResourceModel\Product\Sold\Collection        $reportsProductCollection,
        \Plumrocket\Datagenerator\Model\Template\Space                      $spaceModel,
        \Magento\Framework\Model\ResourceModel\AbstractResource             $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb                       $resourceCollection = null,
        array                                                               $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_cache                       = $cache;
        $this->_filterManager               = $filterManager;
        $this->_dataHelper                  = $dataHelper;
        $this->_categoryFactory             = $categoryFactory;
        $this->_productCollection           = $productCollection;
        $this->_flatProduct                 = $flatProduct;
        $this->_productFactory              = $productFactory;
        $this->_dateTime                    = $dateTime;
        $this->_storeManager                = $storeManager;
        $this->_configurableType            = $configurableType;
        $this->_groupedType                 = $groupedType;
        $this->_bundleType                  = $bundleType;
        $this->_bundlePrice                 = $bundlePrice;
        $this->_stockState                  = $stockState;
        $this->_stockHelper                 = $stockHelper;
        $this->_storeInformation            = $storeInformation;
        $this->_imageHelper                 = $imageHelper;
        $this->_catalogHelper               = $catalogHelper;
        $this->_productHelper               = $productHelper;
        $this->_productAttributeCollection  = $productAttributeCollection;
        $this->_reportsProductCollection    = $reportsProductCollection;
        $this->spaceModel                   = $spaceModel;
    }

    /**
     * Is data preparing at this moment
     * @return boolean
     */
    public function isRunning()
    {
        $time = $this->_cache->load('datafeed_run_' . $this->getTemplate()->getId());
        $maxRunTime = (int)$this->getTemplate()->getData('cache_time');
        if (!$maxRunTime || $maxRunTime > 3600) {
            $maxRunTime = 3600;
        }
        return ($time > time() - $maxRunTime);
    }

    /**
     * Get content
     * @param Template $template
     * @return string
     */
    public function getText($template = null)
    {
        if (!$this->_dataHelper->moduleEnabled()) {
            return '';
        }

        if (null !== $template) {
            $this->setTemplate($template);
        }

        $template = $this->getTemplate();

        $this->_ext         = $template->getExt();
        $this->_replaceFrom = $template->getReplaceFrom();
        $this->_replaceTo   = $template->getReplaceTo();

        $text = $this->getTextCache();

        if (!$text) {
            @ini_set('memory_limit', $this->_memoryLimit);
            $this->_startRun();
            $data = $template->getData();

            $this->_loadCategories();
            $this->_checkEnabledOptions($data);
            $this->_collectionTags($data);
            $this->isConditionEnabled();

            $text = $this->_renderHeader($data['code_header']);
            $text .= $this->_renderItems($data);
            $text .= $this->_renderFooter($data);

            $text = $this->_clean($text);
            $this->_cache->save($text, $this->_getTextCacheKey(), ['datafeed'], (int)$template->getData('cache_time'));

            $this->_endRun();
        }
        return $text;
    }

    /**
     * Get text cache
     * @return string
     */
    public function getTextCache()
    {
        return $this->_cache->load(
            $this->_getTextCacheKey()
        );
    }

    /**
     * Get cache key
     * @return string
     */
    protected function _getTextCacheKey()
    {
        return 'datafeed_' . $this->getTemplate()->getId();
    }

    /**
     * Start run
     * @return $this
     */
    protected function _startRun()
    {
        $this->_cache->save((string)time(), 'datafeed_run_' . $this->getTemplate()->getId(), [], 86400);
        return $this;
    }

    /**
     * Load categories
     * @return $this
     */
    protected function _loadCategories()
    {
        $storeId = $this->getTemplate()->getStoreId();
        $cats = $this->_categoryFactory->create()
            ->getCollection()
            ->addUrlRewriteToResult()
            ->setStoreId($storeId)
            ->addFieldToFilter('is_active', 1)
            ->addAttributeToSelect('*')
            ->load();

        // Any modules might change isActive result in realtime
        foreach ($cats as $cat) {
            $this->_categories[ $cat->getId() ] = $cat;
        }

        return $this;
    }

    /**
     * Checking enabled options
     *
     * @param  array $data
     * @return $this
     */
    protected function _checkEnabledOptions($data)
    {
        $this->_enabledChildsRender = strpos($data['code_item'], '{product.child_items}') !== false;
        $this->_enabledProductCategoryRender = strpos($data['code_item'], '{category.') !== false;
        $this->_enabledSiteRender = strpos($data['code_item'], '{site.') !== false;
        $this->_enabledSoldQty = strpos($data['code_item'], '.sold}') !== false;
        $this->_enabledProductQty = strpos($data['code_item'], '.qty}') !== false;
        $this->_enabledProductStockStatus = strpos($data['code_item'], '.stock_status}') !== false;

        // fix for flat products
        $this->_enabledFlatProducts = $this->_productCollection->isEnabledFlat();

        if ($this->_enabledFlatProducts) {
            $fields = $this->_flatProduct->getAllTableColumns();
            $this->_fields = [];
            foreach (['image', 'small_image', 'thumbnail'] as $field) {
                if (!in_array($field, $this->_fields)) {
                    $this->_fields[] = $field;
                }
            }
        }

        return $this;
    }

    /**
     * Collection tags
     * @param  array $templateData
     * @return $this
     */
    protected function _collectionTags($templateData)
    {
        $tags = [];

        foreach (['code_header', 'code_item', 'code_footer'] as $key) {
            if (preg_match_all('#{([^.}/]+\.[^}]+)}#', $templateData[$key], $matches)) {
                $tags = array_merge($tags, $matches[1]);
            }
        }

        $this->_tags = [];
        foreach ($tags as $tag) {
            if ($tag == 'product.child_items' || $tag == 'product.child') {
                continue;
            }
            $parts = explode('|', $tag);
            list($type, $field) = explode('.', array_shift($parts));
            $filters = [];
            foreach ($parts as $filter) {
                if (false !== strpos($filter, ':')) {
                    list($name, $val) = explode(':', $filter, 2);
                    $filters[$name] = $val;
                }
            }

            if (in_array($field, ['custom_commission', 'custom_commissions_flat_rate'])) {
                $this->shareASale['commission']['enabled'] = true;
            }

            if ($field == 'share_a_sale_subcategory') {
                $this->shareASale['subcategory']['enabled'] = true;
            }

            $this->_tags[] = array_merge(
                $filters,
                [
                    'pattern'   => $tag,
                    'type'      => $type,
                    'field'     => $field,
                ]
            );
        }

        return $this;
    }

    /**
     * Render header
     * @param  string $text
     * @return string
     */
    private function _renderHeader($text)
    {
        $store = $this->_storeManager->getStore($this->getTemplate()->getStoreId());
        $data = [
            'now'       => $this->_dateTime->date('Y-m-d H:i:s'),
            'name'      => $store->getConfig(\Magento\Store\Model\Information::XML_PATH_STORE_INFO_NAME),
            'phone'     => $store->getConfig(\Magento\Store\Model\Information::XML_PATH_STORE_INFO_PHONE),
            'address'   => $this->_storeInformation->getFormattedAddress($store),
            'url'       => $store->getBaseUrl(),
            'count'     => $this->_productsCount,
        ];

        foreach ($this->_tags as $tag) {

            if ($tag['type'] != 'site') {
                continue;
            }

            $val = isset($data[ $tag['field'] ])? $data[ $tag['field'] ] : '';
            $val = $this->_tagFilter($tag, $val);

            $attrib = (isset($tag['attrib']) && $tag['attrib'] == 'yes') ? true : false;
            $text = $this->_renderString('{'. $tag['pattern'] .'}', $val, $text, $attrib);

        }

        return $text;
    }

    /**
     * Tag filter
     * @param  string $tag
     * @param  string $val
     * @param  object $obj
     * @return string
     */
    protected function _tagFilter($tag, $val, $obj = null)
    {
        // Date format.
        if (!empty($tag['date_format'])) {
            $time = is_numeric($val)? $val : strtotime($val);
            $val = date($tag['date_format'], $time);
        }

        // Replace.
        if (!empty($tag['replace'])) {
            $replace = explode(':', $tag['replace'], 2);
            if (!empty($replace[0])) {
                $val = str_replace($replace[0], (!empty($replace[1])? $replace[1] : ''), $val);
            }
        }

        // Max string length.
        if (!empty($tag['truncate'])) {
            $truncate = explode(':', $tag['truncate']);
            $length = (int)$truncate[0];
            $end = isset($truncate[1])? $truncate[1] : '...';

            if (strlen($val) > $length) {
                $length = max(0, $length - strlen($end));
            } else {
                $end = '';
            }

            $val = substr($val, 0, $length) . ($length? $end : '');
        }

        // Images size.
        $imgFields = ['image_url', 'thumbnail_url', 'small_image_url'];
        if (!empty($tag['size']) && in_array($tag['field'], $imgFields) && is_object($obj)) {
            $size = explode(':', $tag['size']);
            $width = (int) $size[0];
            $height = ! empty($size[1]) ? (int) $size[1] : null;

            if ($width) {
                if ($tag['type'] == 'product') {

                    $imageType = 'new_products_content_widget_grid';
                    if ($tag['field'] == 'image_url') {
                        $imageType = 'product_base_image';
                    } elseif ($tag['field'] == 'thumbnail_url') {
                        $imageType = 'product_page_image_small';
                    }

                    $val = (string)$this->_imageHelper->create()
                            ->init($obj, $imageType)->resize($width, $height)->getUrl();
                }
            }
        }

        return $val;
    }

    /**
     * Render string
     * @param  string $from
     * @param  string $to
     * @param  string $text
     * @param  string $attrib
     * @return string
     */
    protected function _renderString($from, $to, $text, $attrib)
    {
        $to = $this->_clean($to);

        if (is_array($to)) {
            $first = reset($to);
            if (is_string($first) || is_numeric($first)) {
                $to = implode(',', $to);
            } else {
                return $text;
            }
        }

        if ($this->_replaceFrom && is_scalar($this->_replaceFrom) && is_scalar($this->_replaceTo) && is_scalar($to)) {
            $to = str_replace($this->_replaceFrom, $this->_replaceTo, $to);
        }
        if (is_string($to) || is_numeric($to)) {
            switch ($this->_ext) {
                case 'csv':
                    if ($to) {
                        $to = str_replace('"', '""', $to);
                        if (! preg_match('/^[0-9\.]+$/', $to)) {
                            $to = '"' . $to . '"';
                        }
                    }
                    break;
                case 'xml':
                    if ($attrib) {
                        $to = htmlentities($to);
                    } elseif ((strpos($to, '<') !== false)
                        || (strpos($to, '>') !== false)
                        || (strpos($to, '&') !== false)
                    ) {
                        $to = str_replace('<![CDATA[', '', $to);
                        $to = str_replace(']]>', '', $to);
                        $to = '<![CDATA[' . $to . ']]>';
                    }
                    break;
                default:
                    // $to -> $to
            }
            $text = str_replace($from, $to, $text);
        }
        return $text;
    }

    /**
     * Render items
     * @param  array $data
     * @return string
     */
    private function _renderItems($data)
    {
        $count = (int)$data['count'];
        $type = (int)$data['type_feed'];
        $result = '';
        $iter = 0;

        if (Template::ENTITY_FEED_TYPE_PRODUCT == $type) {
            // load sold info if {xxx.sold} exists
            if ($this->_enabledSoldQty) {
                $this->_loadSoldQty();
            }

            // get select attributes
            $attributes = $this->_productAttributeCollection->getItems();

            foreach ($attributes as $attribute) {

                if (($attribute->getData('frontend_input') == 'select')
                    && $attribute->usesSource()
                ) {
                    $options = $attribute->getSource()->getAllOptions(false);
                    foreach ($options as $item) {
                        if (!is_array($item['value'])) {
                            $this->_selectAttributes[$attribute->getData('attribute_code')][(string)$item['value']] = $item['label'];
                        }
                    }
                }
            }

            $currPage = 0;
            $lastProductId = 999999;

            do {
                // get products
                $collection = $this->_productFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('entity_id', ['lt' => $lastProductId])
                    ->addUrlRewrite()
                    ->addAttributeToSelect('*')
                    ->addWebsiteFilter();
                    $collection->setFlag('has_stock_status_filter', true); //add load out_stock products

                // fix for flat products
                if ($this->_enabledFlatProducts) {
                    foreach ($this->_fields as $field) {
                        $collection->joinAttribute($field, 'catalog_product/image', 'entity_id', null, 'left');
                    }
                }

                $collection->getSelect()
                    ->limit(500)
                    ->order('e.entity_id DESC');

                $this->_stockHelper->addInStockFilterToCollection($collection);

                foreach ($collection as $product) {
                    $lastProductId = $product->getId();
                    if ($this->enabledCondition) {
                        $space = $this->spaceModel->getSpace($product);
                        $res = $this->getTemplate()->validate($space)
                                ? $this->_renderProduct($product, $data['code_item'])
                                : null;
                    } else {
                        $res = $this->_renderProduct($product, $data['code_item']);
                    }
                    if ($res) {
                        $iter++;
                        if (($count > 0) && ($iter > $count)) {
                            break;
                        }
                        $this->_productsCount++;

                        $result .= "\n" . $res;
                    }
                }
                $currPage++;

            } while (($collection->count() > 0) && (($count == 0) || ($iter < $count)));

        } elseif (Template::ENTITY_FEED_TYPE_CATEGORY == $type) {
            foreach ($this->_categories as $cat) {
                $res = $this->_renderCategory($cat, $data['code_item']);
                if ($res) {
                    $iter++;
                    if (($count > 0) && ($iter > $count)) {
                        break;
                    }
                    $result .= "\n" . $res;
                }
            }
        }
        return $result;
    }

    /**
     * init condition settings
     * @return boolean
     */
    protected function isConditionEnabled()
    {
        $conditions = $this->getTemplate()->getConditions()->asArray();
        if ($this->enabledCondition === null) {
            $this->enabledCondition = isset($conditions['conditions'][0]) && !empty($conditions['conditions'][0]);
        }
        return $this->enabledCondition;
    }

    /**
     * Load sold qty
     * @return $this
     */
    protected function _loadSoldQty()
    {
        // get sold products
        $products = $this->_reportsProductCollection
            ->addOrderedQty();

        foreach ($products as $prod) {
            $this->_soldProducts[ $prod->getId() ] = $prod->getOrderedQty();
        }

        return $this;
    }

    /**
     * Render product
     * @param  \Magento\Catalog\Model\Product $prod product
     * @param  string $text
     * @return string
     */
    protected function _renderProduct($prod, $text)
    {
        $prodCats = $prod->getCategoryIds();

        $storeId = ($this->getTemplate()->getStoreId() != 0) ? $this->getTemplate()->getStoreId() : null;
        $rootCategoryId = $this->_storeManager->getStore($storeId)->getRootCategoryId();

        $cat = null;
        $this->_registry->unregister('current_category');

        foreach ($prodCats as $catId) {
            if (isset($this->_categories[ $catId ]) && $catId != $rootCategoryId) {
                $cat = $this->_categories[ $catId ];

                // Set current category for check product status.
                $this->_registry->register('current_category', $cat);
                break;
            }
        }

        $children = null;
        if ($this->_enabledChildsRender) {
            $children = $this->_loadChildProducts($prod);
        }
        $text = $this->_renderProductEntity($prod, $text, 'product', $children);
        if ($this->_enabledChildsRender) {
            $text = $this->_renderChilds($prod, $children, $text);
        }

        // Render site tags in code item
        if ($this->_enabledSiteRender) {
            $text = $this->_renderHeader($text);
        }

        // Render category
        if ($this->_enabledProductCategoryRender) {
            $text = $this->_renderCategory($cat, $text, $prod);
        }

        return $text;
    }

    /**
     * Load child products
     * @param \Magento\Catalog\Model\Product $product
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _loadChildProducts($product)
    {
        $productId = $product->getId();
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $ids = $this->_configurableType->getChildrenIds($productId);
        } elseif ($product->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $ids = $this->_groupedType->getChildrenIds($productId);
        } elseif ($product->getTypeId() === \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            $ids = $this->_bundleType->getChildrenIds($productId);
        } else {
            $ids = [];
        }

        if (count($ids)) {
            return $this->_productFactory->create()
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', ['in' => [$ids]])
                ->load();
        }

        return [];
    }

    /**
     * Render product entity
     * @param  \Magento\Catalog\Model\Product $prod
     * @param  string $text
     * @param  string $type
     * @param  \Magento\Catalog\Model\ResourceModel\Product\Collection    $children
     * @return string
     */
    protected function _renderProductEntity($prod, $text, $type, $children = null)
    {
        $data = $this->_getProductData($prod, $this->_registry->registry('current_category'), $children);

        foreach ($this->_tags as $tag) {
            if ($tag['type'] != $type) {
                continue;
            }

            $val = isset($data[ $tag['field'] ])? $data[ $tag['field'] ] : '';
            $val = $this->_tagFilter($tag, $val, $prod);
            $attrib = (isset($tag['attrib']) && $tag['attrib'] == 'yes') ? true : false;
            $text = $this->_renderString('{'. $tag['pattern'] .'}', $val, $text, $attrib);
        }

        return $text;
    }

    /**
     * Get product data
     * @param \Magento\Catalog\Model\Product $prod
     * @param \Magento\Catalog\Model\Category $cat
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $children
     * @return array
     */
    protected function _getProductData($prod, $cat, $children)
    {
        $data = $prod->getData();

        $store = $this->_storeManager->getStore($this->getTemplate()->getStoreId());

        $baseUrl = $store->getBaseUrl();

        if (!isset($data['price']) || !$data['price']) {
            // fix price for bundle
            if ($prod->getTypeId() === \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
                $data['price'] = $this->_bundlePrice->getTotalPrices($prod, 'min', 1);
            } elseif ($prod->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
                $price = 0;

                //In magento 2 grouped product havent price, thats why i'm load child product
                if (!$children) {
                    $children = $this->_loadChildProducts($prod);
                }

                if ($children) {
                    foreach ($children as $child) {
                        $_price = $child->getPrice();
                        if (($price == 0) || ($price > $_price)) {
                            $price = $_price;
                        }
                    }
                }
                $data['price'] = $price;
            } else {
                $data['price'] = 0;
            }
        }

        $specialPrice = 0;
        if (isset($data['special_price']) && (int)$data['special_price'] > 0) {
            $specialPrice = $data['special_price'];
        } elseif (isset($data['price'])) {
            $specialPrice = $data['price'];
        }

        $attributeThumbnail = $prod->getResource()->getAttribute('thumbnail');
        $thumbnailUrl = $attributeThumbnail->getFrontend()->getUrl($prod);

        $data = array_merge(
            $data,
            [
                'id'                => $prod->getId(),
                'url'               => ($prod->getData('url_path'))
                                        ? $baseUrl . $prod->getUrlPath()
                                        : $prod->getProductUrl(),

                'image_url'         => (string)$this->_productHelper->getImageUrl($prod),
                'small_image_url'   => (string)$this->_productHelper->getSmallImageUrl($prod),
                'thumbnail_url'     => (string)$thumbnailUrl,

                'sold'              => isset($this->_soldProducts[ $prod->getId() ])
                                        ? (int)$this->_soldProducts[ $prod->getId() ]
                                        : 0,
                'price'             => round($data['price'], 2),
                'price_with_tax'    => $this->_catalogHelper->getTaxPrice($prod, $data['price'], true),
                'price_without_tax' => $this->_catalogHelper->getTaxPrice($prod, $data['price'], false),
                'special_price'     => round($specialPrice, 2),
                'is_bundle' => $prod->getTypeId() === \Magento\Bundle\Model\Product\Type::TYPE_CODE ? 'yes' : 'no'
            ]
        );

        //Magento 2 haven't attribute short description
        //That's why it's trim description
        if (!isset($data['short_description']) && isset($data['description'])) {
            $data['short_description'] = $this->_filterManager->truncate(
                strip_tags($data['description']),
                ['length' => 255, 'etc' => '...', 'remainder' => '', 'breakWords' => false]
            );
        }

        if ($this->_enabledProductQty) {
            $qty = 0;
            if ($prod['type_id'] == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $associated_products = $this->loadByAttribute($prod, 'sku', $data['sku'])
                    ->getTypeInstance()
                    ->getUsedProducts($prod);
                foreach ($associated_products as $assoc) {
                    $assocProduct = $this->_productFactory->create()->load($assoc->getId());
                    $qty += (int)$this->_stockState->getStockQty($assocProduct->getId());
                }
            } else {

                $qty = (int)$this->_stockState->getStockQty($prod->getId());
            }
            $data['qty'] = $qty;
        }

        if ($this->_enabledProductStockStatus) {
            $data['stock_status'] = $prod->getQuantityAndStockStatus()
                ? 'in stock'
                : 'out of stock';
        }

        //For special ShareASale fields
        if ($this->shareASale['commission']['enabled']) {
            $data = $this->getShareASaleCommission($data, $cat);
        }

        if ($this->shareASale['subcategory']['enabled']) {
            $data = $this->getShareASaleCategory($data, $cat);
        }

        // replace select attributes to their values
        foreach ($data as $code => $val) {
            // for ShareASale
            if ($this->shareASale['commission']['enabled'] && in_array($code, ['custom_commissions_flat_rate'])) {
                continue;
            }
            if (isset($this->_selectAttributes[$code])
                && isset($this->_selectAttributes[$code][$val])
            ) {
                $data[$code] = $this->_selectAttributes[$code][$val];
            }
        }

        return $data;
    }

    public function getShareASaleFromProduct($data, $type, $field)
    {
        if (isset($this->shareASale[$type]['products'][$data['id']])
            && is_array($this->shareASale[$type]['products'][$data['id']])
        ) {
            return $this->shareASale[$type]['products'][$data['id']];
        }

        return !(!isset($data[$field]) || isset($data[$field])
            && (!$data[$field] || is_object($data[$field]) && !$data[$field]->getArguments())
        );
    }

    protected function getShareASaleCommission($data, $cat)
    {
        if (isset($data['custom_commission']) && $data['custom_commission'] === 'D') {
            $data['custom_commissions_flat_rate'] = '';
            return $data;
        }
        //search in product
        $fromProduct = $this->getShareASaleFromProduct($data, 'commission', 'custom_commissions_flat_rate');
        if ($fromProduct) {
            if (!is_array($fromProduct)) {
                $this->shareASale['commission']['products'][$data['id']] = [
                    'custom_commission'            => (int)$data['custom_commission'],
                    'custom_commissions_flat_rate' => --$data['custom_commissions_flat_rate'],
                ];
            }
            return array_merge($data, $this->shareASale['commission']['products'][$data['id']]);
        }

        //if need search in category
        $this->getShareASaleCommissionByCategory($cat);

        if (empty($this->shareASale['commission'][$cat->getId()])) {
            $data['custom_commission'] = 'D';
            $data['custom_commissions_flat_rate'] = '';
        } else {
            $this->shareASale['commission']['products'][$data['id']] = $this->shareASale['commission'][$cat->getId()];
            $data = array_merge($data, $this->shareASale['commission'][$cat->getId()]);
        }

        return $data;
    }

    protected function getShareASaleCommissionByCategory($category)
    {
        $categoryId = (int)(is_object($category) ? $category->getId() : $category);
        if (!$categoryId) {
            return null;
        }

        if (isset($this->shareASale['commission'][$categoryId])) {
            return $this->shareASale['commission'][$categoryId];
        }

        if (!is_object($category)) {
            if (isset($this->_categories[$categoryId])) {
                $category = $this->_categories[$categoryId];
            } else {
                $category = $this->_categoryFactory->create()->load($categoryId);
                $this->_categories[$categoryId] = $category;
            }
        }

        $shareASale = [];
        if ($category->getCustomCommissionsFlatRate() == 0) {
            $shareASale = $this->getShareASaleCommissionByCategory($category->getParentId());
        } elseif (is_numeric($category->getCustomCommissionsFlatRate())) {
            $shareASale = [
                'custom_commission' => (int)$category->getCustomCommission(),
                'custom_commissions_flat_rate' => is_object($category->getCustomCommissionsFlatRate())
                                                    ? $category->getCustomCommissionsFlatRate()->getArguments() - 1
                                                    : (int)$category->getCustomCommissionsFlatRate() - 1,
            ];
        }

        return $this->shareASale['commission'][$categoryId] = $shareASale;
    }

    protected function getShareASaleCategory($data, $cat)
    {
        $fromProduct = $this->getShareASaleFromProduct($data, 'subcategory', 'share_a_sale_subcategory');
        if ($fromProduct) {
            return array_merge(
                $data,
                [
                    'share_a_sale_subcategory' => $data['share_a_sale_subcategory'],
                    'share_a_sale_category' => \Plumrocket\Datagenerator\Model\Source\ShareASaleCategory::getCategoryIdBySubcategory($data['share_a_sale_subcategory']),
                ]);
        }

        //if need search in category
        $this->getShareASaleSubCategoryByCategory($cat);

        if (empty($this->shareASale['subcategory'][$cat->getId()])) {
            $data['share_a_sale_category'] = '';
            $data['share_a_sale_subcategory'] = '';
        } else {
            $data = array_merge($data, $this->shareASale['subcategory'][$cat->getId()]);
        }

        return $data;
    }

    protected function getShareASaleSubCategoryByCategory($category)
    {
        $categoryId = (int)(is_object($category) ? $category->getId() : $category);
        if (!$categoryId) {
            return null;
        }

        if (isset($this->shareASale['subcategory'][$categoryId])) {
            return $this->shareASale['subcategory'][$categoryId];
        }

        if (!is_object($category)) {
            if (isset($this->_categories[$categoryId])) {
                $category = $this->_categories[$categoryId];
            } else {
                $category = $this->_categoryFactory->create()->load($categoryId);
                $this->_categories[$categoryId] = $category;
            }
        }

        $result = [];
        if ($category->getShareASaleSubcategory() == 0) {
            $result = $this->getShareASaleSubCategoryByCategory($category->getParentId());
        } elseif (is_numeric($category->getShareASaleSubcategory())) {
            $result = [
                'share_a_sale_subcategory' => is_object($category->getShareASaleSubcategory())
                                                    ? $category->getShareASaleSubcategory()->getArguments()
                                                    : $category->getShareASaleSubcategory(),
            ];
            if ($result['share_a_sale_subcategory']) {
                $result['share_a_sale_category'] = \Plumrocket\Datagenerator\Model\Source\ShareASaleCategory::getCategoryIdBySubcategory($result['share_a_sale_subcategory']);
            }
        }

        return $this->shareASale['subcategory'][$categoryId] = $result;
    }

    public function loadByAttribute($product, $attribute, $value, $additionalAttributes = '*')
    {
        $collection = $product->getResourceCollection()
            ->addAttributeToSelect($additionalAttributes)
            ->addAttributeToFilter($attribute, $value)
            ->setPage(1, 1)
            ->setFlag('has_stock_status_filter', true);

        foreach ($collection as $object) {
            return $object;
        }
        return false;
    }

    /**
     * Render childs
     * @param  \Magento\Catalog\Model\Product $prod
     * @param  $products
     * @param  string $text
     * @return string
     */
    protected function _renderChilds($prod, $products, $text)
    {
        preg_match_all('/[\s]*\{product\.child_items\}(.*)\{\/product\.child_items\}[\s]*/smU', $text, $sections);

        if (isset($sections[1])) {
            foreach ($sections[1] as $section_id => $section_text) {
                if ($products) {
                    preg_match_all('/[\s]*\{product\.child\}(.*)\{\/product\.child\}[\s]*/smU', $section_text, $blocks);

                    if (isset($blocks[1])) {
                        foreach ($blocks[1] as $block_id => $block_text) {
                            $block_text = rtrim($block_text);
                            $products_text = '';

                            foreach ($products as $pr) {
                                $products_text .= $this->_renderProductEntity($pr, $block_text, 'child');
                            }
                            $section_text = str_replace($blocks[0][$block_id], $products_text, $section_text);
                        }
                    }
                } else {
                    $section_text = '';
                }
                $text = str_replace($sections[0][$section_id], $section_text, $text);
            }
        }
        return $text;
    }

    /**
     * Render category
     * @param \Magento\Catalog\Model\Category $cat
     * @param  string $text
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function _renderCategory($cat, $text, $product = null)
    {
        $data = $this->_getCategoryData($cat, $product);

        if ($cat) {
            $data['url'] = $cat->getUrl();
        }

        foreach ($this->_tags as $tag) {
            if ($tag['type'] != 'category') {
                continue;
            }

            $val = isset($data[ $tag['field'] ])? $data[ $tag['field'] ] : '';
            $val = $this->_tagFilter($tag, $val, $cat);
            $attrib = (isset($tag['attrib']) && $tag['attrib'] == 'yes') ? true : false;
            $text = $this->_renderString('{'. $tag['pattern'] .'}', $val, $text, $attrib);
        }

        return $text;
    }

    /**
     * Get category data
     * @param \Magento\Catalog\Model\Category $cat
     * @param \Magento\Catalog\Model\Product|null $product
     * @return array|null
     */
    protected function _getCategoryData($cat, $product = null)
    {
        if (!is_object($cat)) {
            return;
        }

        $data = $cat->getData();
        $store = $this->_storeManager->getStore($this->getTemplate()->getStoreId());
        $template = $this->getTemplate();

        if ((int) $template->getTemplateId() === $template->getGoogleShoppingId()) {
            $data['name'] = $product && $product->getData('google_product_category')
                ? $product->getData('google_product_category')
                : $cat->getData('google_product_category');
        }

        $data = array_merge(
            $data,
            [
                'id'                => $cat->getId(),
                'breadcrumb_path'   => (string)$this->_getBreadcrumbPath($cat),
                'image_url'         => $cat->getImageUrl(),
                'thumbnail_url'     => $cat->getThumbnailUrl()
            ]
        );

        if (isset($data['url_path'])) {
            $data['url'] = str_replace('?___SID=U', '', $store->getUrl($data['url_path']));
        }

        return $data;
    }

    /**
     * Retrieve breadcrumb path
     * @param  \Magento\Catalog\Model\Category $category
     * @return string
     */
    protected function _getBreadcrumbPath($category)
    {
        $path = [];
        $pathInStore = $category->getPathInStore();

        $pathIds = array_reverse(explode(',', $pathInStore));

        $categories = $category->getParentCategories();

        // add category path breadcrumb
        foreach ($pathIds as $categoryId) {
            if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                $path[] = $categories[$categoryId]->getName();
            }
        }

        return implode(' > ', $path);
    }

    /**
     * Render footer
     * @param  array $data
     * @return string
     */
    protected function _renderFooter($data)
    {
        return "\n" . $this->_renderHeader($data['code_footer']);
    }

    /**
     * Clean
     * @param  string|array $text
     * @return string
     */
    protected function _clean($text)
    {
        $noArray = $text;
        if (is_array($text)) {
            $noArray = implode(',', $text);
        }

        preg_match_all(
            '/[\s]*\{no_(br|html|quotes|br_html){1}\}(.*)\{\/no_(br|html|quotes|br_html){1}\}[\s]*/smU',
            $noArray,
            $nodes,
            PREG_PATTERN_ORDER
        );

        if ($nodes[1]) {
            foreach ($nodes[1] as $key => $no_item) {
                $node_text = '';
                switch ($no_item) {
                    case 'br_html':
                        $node_text = rtrim(
                            str_replace(["\r", "\n"], ' ', $nodes[2][$key])
                        );
                        $node_text = strip_tags($node_text);
                        if (!empty($node_text) && 'xml' === $this->_ext) {
                            $node_text = '<![CDATA[' . $node_text;
                        }
                        break;
                    case 'br':
                        $node_text = rtrim(
                            str_replace(["\r", "\n"], ' ', $nodes[2][$key])
                        );
                        break;
                    case 'html':
                        $node_text = strip_tags($nodes[2][$key]);

                        if ('xml' === $this->_ext) {
                            $node_text = '<![CDATA[' . $node_text;
                        }
                        break;
                    case 'quotes':
                        $node_text = str_replace('"', '', $nodes[2][$key]);
                        break;
                }
                if ($noArray) {
                    $noArray = str_replace($nodes[0][$key], $node_text, $noArray);
                }
            }
        }
        return preg_replace('/\{(product|category|site|child|no_)\.[a-z0-9\_]+\}/', '', $noArray);
    }

    /**
     * end run
     * @return $this
     */
    protected function _endRun()
    {
        $this->_cache->remove('datafeed_run_' . $this->getTemplate()->getId());
        return $this;
    }
}
