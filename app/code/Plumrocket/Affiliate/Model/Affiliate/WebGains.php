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

use Magento\Catalog\Model\Product;

class WebGains extends AbstractModel
{
    /**
     * Save categories mapping
     * @var array
     */
    protected $categoriesEventId = [];

    /**
     * Save products mapping
     * @var array
     */
    protected $productsEventId = [];

    /**
     * Product Repo
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Category Repo
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * WebGains constructor.
     *
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager    $cookieManager
     * @param \Plumrocket\Affiliate\Helper\Data                    $dataHelper
     * @param \Magento\Framework\Model\Context                     $context
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Customer\Model\Session                      $customerSession
     * @param \Magento\Checkout\Model\Session                      $checkoutSession
     * @param \Magento\Framework\App\RequestInterface              $request
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager
     * @param \Magento\Catalog\Model\ProductFactory                $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory               $categoryFactory
     * @param \Magento\Sales\Model\OrderFactory                    $orderFactory
     * @param \Magento\Directory\Model\RegionFactory               $regionFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Catalog\Helper\Image                        $imageHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfigInterface
     * @param \Magento\Framework\Url                               $url
     * @param \Magento\Catalog\Api\ProductRepositoryInterface      $productRepository
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface     $categoryRepository
     * @param null                                                 $resource
     * @param null                                                 $resourceCollection
     * @param array                                                $data
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
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        $resource = null,
        $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $cookieManager,
            $dataHelper,
            $context,
            $registry,
            $customerSession,
            $checkoutSession,
            $request,
            $storeManager,
            $productFactory,
            $categoryFactory,
            $orderFactory,
            $regionFactory,
            $remoteAddress,
            $imageHelper,
            $scopeConfigInterface,
            $url,
            $resource,
            $resourceCollection,
            $data
        );
        $this->productRepository  = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get program id
     *
     * @return int
     */
    public function getProgramId()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['program_id']) ? $additionalData['program_id'] : '';
    }

    /**
     * Get event id
     *
     * @return int
     */
    public function getEventId($item = null)
    {
        $eventId = '';

        // from product
        if ($item) {
            $eventId = $this->getEventIdByProductId($item->getProductId());
        }

        // from config
        if (!$this->isValidEventId($eventId)) {
            $additionalData = $this->getAdditionalDataArray();
            $eventId = isset($additionalData['event_id']) ? $additionalData['event_id'] : '';
        }

        return $eventId;
    }

    /**
     * Validate event Id
     *
     * @param  mixed $eventId
     * @return boolean
     */
    protected function isValidEventId($eventId)
    {
        return ($eventId !== null && $eventId !== '');
    }

    /**
     * Retrieve Event Id from product
     *
     * @param $productId
     * @return mixed
     */
    protected function getEventIdByProductId($productId)
    {
        if ($productId) {
            if (!isset($this->productsEventId[$productId])) {
                /**
                 * @var $product Product
                 */
                $product = $this->productRepository->getById($productId);
                $this->productsEventId[$productId] = $product->getData('affiliate_webgains_eventid');
            }
        }

        // from categories
        if (isset($product) && !$this->isValidEventId($this->productsEventId[$productId])) {
            $this->productsEventId[$productId] = $this->getEventIdByCategoryIds($product->getCategoryIds());
        }

        return $this->productsEventId[$productId];
    }

    /**
     * Get event id from categories
     *
     * @param array $categoryIds
     * @return int | null
     */
    protected function getEventIdByCategoryIds($categoryIds)
    {
        $eventId = null;
        if (count($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                if (!isset($this->categoriesEventId[$categoryId])) {
                    $category = $this->categoryRepository->get($categoryId);
                    $this->categoriesEventId[$categoryId] = $category->getData('affiliate_webgains_eventid');
                }
                if ($this->isValidEventId($this->categoriesEventId[$categoryId])) {
                    $eventId = $this->categoriesEventId[$categoryId];
                    break;
                }
            }
        }

        return $eventId;
    }

    /**
     * Get pin
     *
     * @return int
     */
    public function getPin()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['pin_id']) ? $additionalData['pin_id'] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = null;
        $scheme = $this->_request->getScheme();

        if ($_section == parent::SECTION_BODYEND) {
            $order = $this->getLastOrder();
            if ($order && $order->getId()) {

                $coupon = $order->getCouponCode() ? $order->getCouponCode() : '';

                /* wgitems - (optional) should contain pipe separated list of shopping basket items. Fields for each item are seperated by double colon.
                    First field is commission type, second field is price of item, third field (optional) is name of item, fourth field (optional) is product code/id, fifth field (optional) is voucher code. Example for two items; items=1::54.99::Harry%20Potter%20dvd::hpdvd93876|5::2.99::toothbrush::tb287::voucher1    */
                $products = [];
                foreach ($order->getAllVisibleItems() as $item) {
                    $itemStr = implode('::', [
                        $this->getEventId($item), // Event ID
                        round($item->getPrice(), 2), // Product Price
                        rawurlencode($item->getName()), // Product Name
                        rawurlencode($item->getSku()), // Product ID
                        rawurlencode($coupon), // Voucher Code
                    ]);
                    $qtyOrdered = (int)$item->getQtyOrdered();

                    for ($qty = 1; $qty <= $qtyOrdered; $qty++) {
                        $products[] = $itemStr;
                    }
                }

                if ($products && $coupon) {
                    $products[] = implode('::', [
                        $this->getEventId(), // Event ID
                        round($order->getBaseDiscountAmount(), 2), // Discount
                        rawurlencode($coupon), // Voucher Code
                        rawurlencode($coupon), // Voucher Code
                    ]);
                }

                $amount = round($order->getBaseSubtotal() - abs($order->getBaseDiscountAmount()), 2);

                $params = [
                    'wgver'             => '1.2',
                    'wgsubdomain'       => 'track',
                    'wglang'            => $this->getLocaleCode(),
                    'wgslang'           => 'php',
                    'wgprogramid'       => $this->getProgramId(),
                    'wgeventid'         => $this->getEventId(),
                    'wgvalue'           => $amount,
                    'wgorderreference'  => rawurlencode($order->getIncrementId()),
                    'wgcomment'         => '',
                    'wgmultiple'        => '1',
                    'wgitems'           => implode('|', $products),
                    'wgcustomerid'      => '', // please do not use without contacting us first
                    'wgproductid'       => '', // please do not use without contacting us first
                    'wgvouchercode'     => rawurlencode($coupon),
                ];

                $params = array_merge(
                    $params,
                    [
                        'wgchecksum'        => md5($this->getPin() . implode('&', $params)), //@codingStandardsIgnoreLine
                        'wgrs'              => '1',
                        'wgprotocol'        => $scheme,
                        'wgcurrency'        => $this->getCurrencyCode($order),
                    ]
                );

                $html = '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                                <img src="'. $scheme .'://'. $params['wgsubdomain'] .'.webgains.com/transaction.html?'. urldecode(http_build_query($params)) .'" width="1" height="1" />
                            </div>';
            }
        }

        return $html;
    }
}
