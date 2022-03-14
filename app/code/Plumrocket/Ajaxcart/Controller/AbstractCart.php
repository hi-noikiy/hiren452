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

namespace Plumrocket\Ajaxcart\Controller;

use Plumrocket\Ajaxcart\Helper\Blocks;
use Plumrocket\Ajaxcart\Helper\Data as DataHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Session;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Downloadable\Model\Product\Type as DownloadableProductType;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Escaper;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedProduct;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Magento\Wishlist\Model\ItemFactory as WishlistItemFactory;
use Magento\Wishlist\Model\LocaleQuantityProcessor  as QuantityProcessor;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Wishlist\Model\ResourceModel\Item\Option\CollectionFactory as WishlistOptionCollection;
use Psr\Log\LoggerInterface;

class AbstractCart extends \Magento\Checkout\Controller\Cart
{
    /**
     * @var string
     */
    protected $blocksHtml = null;

    /**
     * @var integer
     */
    protected $addedQty;

    /**
     * @var type \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Plumrocket\Ajaxcart\Helper\Blocks
     */
    protected $blocksHelper;

    /**
     * @var \Plumrocket\Ajaxcart\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Magento\Wishlist\Model\ItemFactory
     */
    protected $wishlistItemFactory;

    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Item\Option\CollectionFactory
     */
    protected $wishlistOptionCollection;

    /**
     * @var \Magento\Wishlist\Model\LocaleQuantityProcessor
     */
    protected $quantityProcessor;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $wishlistHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    protected $downloadableProductType;

    /**
     * @param Blocks $blocksHelper
     * @param DataHelper $dataHelper
     * @param ResolverInterface $localeResolver
     * @param JsonFactory $resultJsonFactory
     * @param Escaper $escaper
     * @param LoggerInterface $logger
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param WishlistFactory $wishlistFactory,
     * @param WishlistItemFactory $wishlistItemFactory,
     * @param WishlistOptionCollection $wishlistOptionCollection
     * @param QuantityProcessor $quantityProcessor
     * @param ProductHelper $productHelper
     * @param WishlistHelper $wishlistHelper
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Blocks $blocksHelper,
        DataHelper $dataHelper,
        ResolverInterface $localeResolver,
        JsonFactory $resultJsonFactory,
        Escaper $escaper,
        LoggerInterface $logger,
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        PageFactory $resultPageFactory,
        Registry $registry,
        WishlistFactory $wishlistFactory,
        WishlistItemFactory $wishlistItemFactory,
        WishlistOptionCollection $wishlistOptionCollection,
        QuantityProcessor $quantityProcessor,
        ProductHelper $productHelper,
        WishlistHelper $wishlistHelper,
        CustomerSession $customerSession,
        DownloadableProductType $downloadableProductType
    ) {
        $this->blocksHelper = $blocksHelper->setController($this);
        $this->dataHelper = $dataHelper;
        $this->localeResolver = $localeResolver;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->escaper = $escaper;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistItemFactory = $wishlistItemFactory;
        $this->wishlistOptionCollection = $wishlistOptionCollection;
        $this->quantityProcessor = $quantityProcessor;
        $this->productHelper = $productHelper;
        $this->wishlistHelper = $wishlistHelper;
        $this->customerSession = $customerSession;
        $this->downloadableProductType = $downloadableProductType;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
    }

    /**
     *
     */
    public function execute()
    {
        parent::execute();
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product') ?: $this->getRequest()->getParam('productId');
        if ($productId) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Send json response with html
     * @param bool $success
     * @return string
     */
    protected function sendResponse($success = false)
    {
        $this->_eventManager->dispatch(
            'plumrocket_ajaxcart_before_get_response_data',
            [
                'controller' => $this
            ]
        );

        $itemsId = array_keys($this->dataHelper->getAddedQuoteItems());

        $data = [
            'success'   => $success,
            'html'      => ($html = $this->getBlocksHtml()) ? $html : null,
            'qty'       => $this->cart->getSummaryQty(),
            'messages'  => ($messages = $this->getMessages(true)) ? $messages : null,
            'items_id'  => $itemsId,
            'fullActionName' => $this->_request->getFullActionName(),
        ];

        $this->_eventManager->dispatch(
            'plumrocket_ajaxcart_before_set_response_data',
            [
                'controller' => $this,
                'data' => &$data
            ]
        );

        $result = $this->resultJsonFactory->create();
        return $result->setData($data);
    }

    /**
     * Create html-code for ajax response
     * @return string
     */
    protected function getBlocksHtml()
    {
        if (is_null($this->blocksHtml)) {
            $request = $this->getRequest();
            $this->blocksHtml = $this->blocksHelper->getBlocksHtml(
                $request->getParam('htmlBlocks'),
                $request->getParam('isCartPage') ? 'checkout_cart_index' : null
            );
        }
        return $this->blocksHtml;
    }

    /**
     * Create html-code of product with options for addconfiguration-popup
     * @param string|integer $productId
     * @param array|null $params
     * @return string
     */
    protected function prepareProductBlockHtml($productId, $params = null)
    {
        $this->blocksHtml = $this->blocksHelper
            ->prepareProductLayout($productId, $params)
            ->getBlocksHtml('product.info', null, false);
        return $this;
    }

    /**
     * @param bool $clear
     * @return array
     */
    protected function getMessages($clear = false)
    {
        $msgs = $this->messageManager->getMessages($clear)->getItems();
        $messages = [];
        foreach($msgs as $msg) {
            $messages[$msg->getType()][] = $msg->getText();
        }
        return $messages;
    }

    /**
     * Description return Wishlist
     * @param int|string|null $wishlistId
     * @return \Magento\Wishlist\Model\Wishlist
     */
    protected function getWishlist($wishlistId = null)
    {
        $wishlist = $this->registry->registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = $this->customerSession->getCustomerId();
            /* @var \Magento\Wishlist\Model\Wishlist $wishlist */
            $wishlist = $this->wishlistFactory->create();
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                $this->logger->critical(__("Requested wishlist doesn't exist"));
            }

            $this->registry->register('wishlist', $wishlist);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            return false;

        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                $this->logger->critical(__('Wishlist could not be created.'))
            );
            return false;
        }

        return $wishlist;
    }

    /**
    * Is product configurable
    *
    * @return bool
    */
    protected function isConfigure($product)
    {
        if (!$product->getId()) {
            return false;
        }

        $pInstance = $product->getTypeInstance(true);

        if ($pInstance->hasRequiredOptions($product)
            || ($pInstance instanceof GroupedProduct)
            || $product->getTypeId() == ProductType::TYPE_BUNDLE
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param $cart
     * @param $product
     * @return string|void
     */
    protected function afterAdd($cart, $product)
    {
        $request = $this->getRequest();

        if ($hasError = $cart->getQuote()->getHasError()) {
            $messages = $this->getMessages();
            if (empty($messages['error']) && empty($messages['notice'])) {
                $hasError = false;
            }
        }

        if (!$hasError) {
            $htmlBlocks = $request->getParam('htmlBlocks');
            $htmlBlocks[] = 'product.info';
            $request->setParam('htmlBlocks', $htmlBlocks);
            $this->registry->register('qty_added', $this->addedQty);
            $this->_forward('addinfo');
            return;

            $message = __(
                'Congradulations!!! You added %1 to your shopping cart.',
                $product->getName()
            );
            $this->messageManager->addSuccessMessage($message);

            return $this->sendResponse(true);
        }

        return $this->sendResponse();
    }

    /**
     * @return \Magento\Framework\App\ViewInterface
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * @return PageFactory
     */
    public function getResultPage()
    {
        return $this->resultPageFactory;
    }

    /**
     * @param \Magento\Catalog\Model\Product  $product
     * @return bool
     */
    protected function canAdd($product)
    {
        if ($product->getTypeId() == DownloadableProductType::TYPE_DOWNLOADABLE) {
            return !($this->downloadableProductType->getLinkSelectionRequired($product)
                    && empty($this->_request->getParam('links')));
        }
        return true;
    }

    protected function redirectToProductPage($url) {
        $data = [
            'success' => false,
            'action' => 'redirect',
            'productUrl' => base64_encode($url)
        ];
        $result = $this->resultJsonFactory->create();
        return $result->setData($data);
    }
}
