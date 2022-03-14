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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Checkout\Helper\Cart;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Registry;
use Magento\Checkout\Model\Cart as CartModel;
use Plumrocket\Ajaxcart\Helper\Data as DataHelper;

class AddInfo extends \Magento\Checkout\Block\Cart
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollection;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CartModel
     */
    protected $cart;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param Url $catalogUrlBuilder
     * @param Cart $cartHelper
     * @param HttpContext $httpContext
     * @param ProductFactory $productFactory
     * @param CategoryCollectionFactory $categoryCollection
     * @param Registry $registry
     * @param CartModel $cart
     * @param DataHelper $dataHelper
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        Url $catalogUrlBuilder,
        Cart $cartHelper,
        HttpContext $httpContext,
        ProductFactory  $productFactory,
        CategoryCollectionFactory $categoryCollection,
        Registry $registry,
        CartModel $cart,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->productFactory = $productFactory;
        $this->categoryCollection = $categoryCollection;
        $this->registry = $registry;
        $this->cart = $cart;
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $catalogUrlBuilder,
            $cartHelper,
            $httpContext,
            $data
        );
    }

    /**
     * get checkout url
     *
     * @return string  url
     */
    public function getCheckoutUrl()
    {
        $helper = $this->dataHelper;

        if ($helper->checkoutBtnAction() === 0 ) {
            return $this->getUrl('checkout/cart');
        } elseif ($helper->checkoutBtnAction() === 1) {
            return $this->getUrl('checkout');
        } else {
            return false;
        }
    }

    /**
     * @return CartModel
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getRegistry($key = '')
    {
        return $this->registry->registry($key);
    }

    /**
     * get continue link url
     *
     * @return string url
     */
    public function getContinueLinkUrl()
    {
        $helper = $this->dataHelper;

        switch($helper->continueShoppingLink()) {
            case 1 :
                if ($this->getRequest()->getParam('isCategory') == 1) {
                    return 'close';
                }

                $categoryId = $this->getRequest()->getParam('categoryId');

                if (!$categoryId) {
                    $productId = $this->getRequest()->getParam('product');
                    $product = $this->productFactory->create()->load($productId);
                    $categories = $product->getCategoryIds();
                    if (is_array($categories) && count($categories)) {
                        $categoryId = $categories[0];
                    }
                }

                if (!$categoryId) {
                    return 'close';
                }

                $category = $this->categoryCollection->create()
                    ->addFieldToFilter('entity_id', $categoryId)
                    ->addUrlRewriteToResult()
                    ->setPageSize(1)
                    ->getFirstItem();

                return $category->getUrl($category);
                break;

            case 2 :
                return $this->escapeUrl($helper->continueCustomLink());
                break;
            default :
                return 'close';
        }
    }
}
