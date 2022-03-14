<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Controller\Product\View\ViewInterface;
use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\ProductKit\Repository\KitItemRepository;
use Mirasvit\ProductKit\Repository\KitRepository;
use Mirasvit\ProductKit\Service\CartService;
use Mirasvit\ProductKit\Service\OfferKitService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends Action implements ViewInterface
{
    private $cart;

    private $cartService;

    private $kitItemRepository;

    private $kitRepository;

    private $offerKitService;

    private $productRepository;

    private $registry;

    private $resultPageFactory;

    private $scopeConfig;

    private $storeManager;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CartService $cartService,
        KitItemRepository $kitItemRepository,
        KitRepository $kitRepository,
        OfferKitService $offerKitService,
        CheckoutCart $cart,
        Registry $registry,
        PageFactory $resultPageFactory,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Context $context
    ) {
        parent::__construct($context);

        $this->cart              = $cart;
        $this->kitItemRepository = $kitItemRepository;
        $this->kitRepository     = $kitRepository;
        $this->offerKitService   = $offerKitService;
        $this->cartService       = $cartService;
        $this->productRepository = $productRepository;
        $this->registry          = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig       = $scopeConfig;
        $this->storeManager      = $storeManager;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $data = $this->filterRequestData();

        // reset data if hash does not exist
        if (!$hash = $this->getHash()) {
            $this->messageManager->addComplexSuccessMessage(
                'addProductKitErrorMessage'
            );
            $data = [];
        }
        $selectedCombination      = $this->getRequest()->getParam('selectedCombination');
        $selectedQuoteCombination = $this->getRequest()->getParam('selectedQuoteCombination');

        # Step 1: Remove same products from the cart
        foreach ($data as $kitId => $kitData) {
            foreach ($kitData as $key => $productData) {
                $productId = $productData['product_id'];

                /** @var \Magento\Quote\Model\Quote\Item $item */
                foreach ($this->cart->getItems() as $item) {
                    $itemOptions = $this->cartService->getItemOptions($item);

                    if (!isset($itemOptions['kit_id']) && $item->getProduct()->getId() == $productId) {
                        $this->cart->removeItem($item->getItemId());
                    }
                }
            }
        }

        # Step 2: Add items to the cart

        $allProductsAdded = true;
        /** Magento add to cart code */
        foreach ($data as $kitId => $kitData) {
            $kitIndexItems = $this->cartService->getKitItems($kitId, $kitData);

            if (count($kitData) != count($kitIndexItems)) {
                $this->messageManager->addComplexSuccessMessage(
                    'addProductKitErrorMessage'
                );
                break;
            }

            $kit = $this->kitRepository->get($kitId);

            if (!$selectedCombination) {
                $offerCombinationKey = [];
                foreach ($kitData as $key => $productData) {
                    $offerCombinationKey[] = (int)$productData['item_id'];
                }
                $selectedCombination = implode('/', $offerCombinationKey);
            }

            if (!$selectedQuoteCombination) {
                $selectedQuoteCombination = $selectedCombination;
            }

            $currentCombination = [];
            foreach ($kitData as $productData) {
                $itemId = $this->cartService->findKitIndexByProduct(
                    $kit->getId(), (int)$productData['product_id'], (string)$productData['position']
                );

                if ($itemId === 0) {
                    $this->messageManager->addComplexSuccessMessage(
                        'addProductKitErrorMessage'
                    );
                    break(2);

                }

                $combinationItem = $this->kitItemRepository->get($itemId);
                $combinationItem->setProductId((int)$productData['product_id']);

                $currentCombination[] = $combinationItem;
            }

            foreach ($currentCombination as $combinationItem) {

                if (!isset($kitData[$combinationItem->getProductId()])) {
                    break;
                }
                $productId   = $combinationItem->getProductId();
                $productData = $kitData[$productId];
                try {
                    $related = $this->getRequest()->getParam('related_product');
                    $product = $this->getProduct($productId);
                    // Check product availability
                    if (!$product) {
                        throw new \Exception(__('Product is not available right now. Please try again later.'));
                    }

                    $productData['hash'] = $hash;

                    $productData['selectedCombination'] = $selectedQuoteCombination;

                    $productData = $this->cartService->prepareProductService($product, $productData);

                    $this->cart->addProduct($product, $productData);

                    if (!empty($related)) {
                        $this->cart->addProductsByIds(explode(',', $related));
                    }
                } catch (\Exception $e) {
                    $allProductsAdded = false;

                    $data[$kitId][$productId]['error'] = $e->getMessage();
                }
            }

            if (empty($data[$kitId])) {
                unset($data[$kitId]);
            }
        }
        if ($allProductsAdded) {
            $this->cart->save();

            unset($data[$kitId]);
        }
        /** end of Magento add to cart code */

        $html    = '';
        $message = '';
        if (count($data)) {
            $html .= $this->renderProductOptions($data);
        } else {
            $message = __('Product kit was successfully added to the cart');
            if ($this->shouldRedirectToCart()) {
                $this->messageManager->addSuccessMessage($message);
            } else {
                $this->messageManager->addComplexSuccessMessage('addProductKitSuccessMessage', [
                    'cart_url' => $this->getCartUrl(),
                ]);
            }
        }

        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $response->setData([
            'success' => $html ? false : true,
            'html'    => $html,
            'message' => $message,
        ]);
    }

    /**
     * Returns cart url
     *
     * @return string
     */
    private function getCartUrl()
    {
        return $this->_url->getUrl('checkout/cart', ['_secure' => true]);
    }

    /**
     * Is redirect should be performed after the kit was added to cart.
     *
     * @return bool
     */
    private function shouldRedirectToCart()
    {
        return $this->scopeConfig->isSetFlag(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function getHash()
    {
        $hash = $this->getRequest()->getParam('hash');

        return base64_decode($hash, true) ? $hash : false;
    }

    private function filterRequestData()
    {
        $data = [];

        $requestData = $this->getRequest()->getParams();

        if (isset($requestData['products'])) {
            $data = $requestData['products'];

            // sorting by position
            foreach ($data as $kitId => $kitData) {
                uasort($data[$kitId], function ($a, $b) {
                    return $a['position'] > $b['position'] ? 1 : -1;
                });
            }
        } else {
            foreach ($requestData['forms'] as $kitId => $kitData) {
                foreach ($kitData as $formDataSerialized) {
                    $formData = [];
                    parse_str($formDataSerialized, $formData);

                    if (isset($formData['product'])) {
                        $formData['product_id'] = $formData['product'];
                    }

                    $data[$kitId][$formData['product_id']] = $formData;
                }
            }
        }

        return $data;
    }

    /**
     * @param int $productId
     *
     * @return \Magento\Catalog\Model\Product|\Magento\Catalog\Model\Product\Type\AbstractType|false
     */
    private function getProduct($productId)
    {
        $storeId = $this->storeManager->getStore()->getId();

        try {
            return $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @param array $requestData
     *
     * @return string
     */
    private function renderProductOptions($requestData)
    {
        try {
            $errors = $productBlocksHtml = [];
            foreach ($requestData as $kitData) {
                foreach ($kitData as $productData) {
                    $productBlocksHtml[] = $this->getProductBlockHtml($productData);
                    if (!empty($productData['error'])) {
                        $errors[] = $productData['error'];
                    }
                }
            }
            $page = $this->resultPageFactory->create(false, ['isIsolated' => true]);

            /** @var \Mirasvit\ProductKit\Block\Cart\CartItems $block */
            $block = $page->getLayout()->createBlock(
                \Mirasvit\ProductKit\Block\Cart\CartItems::class,
                'kit-popup-product-' . $productData['product_id']
            );

            $block->setProductBlocksHtml($productBlocksHtml);
            $block->setErrors($errors);

            return $block->toHtml();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param array $productData
     *
     * @return mixed|string
     */
    private function getProductBlockHtml($productData)
    {
        // Prepare helper and params
        $params = new \Magento\Framework\DataObject();

        $this->registry->unregister('product');
        $this->registry->unregister('current_product');
        $this->registry->unregister('current_category');

        /** @var \Magento\Catalog\Helper\Product $product */
        $product = $this->_objectManager->create(\Magento\Catalog\Helper\Product::class);
        $product->initProduct($productData['product_id'], $this, $params);

        $currentProduct = $this->registry->registry('product');

        $page = $this->resultPageFactory->create(false, ['isIsolated' => true]);
        $page->addHandle('catalog_product_view');
        $page->addHandle('catalog_product_view_type_' . $currentProduct->getTypeId());
        $layout = $page->getLayout();

        $currentProduct->setCustomOptions([]); // fix error for bundle products
        /** @var \Mirasvit\ProductKit\Block\Cart\CartItem $block */
        $block = $layout->createBlock(
            \Mirasvit\ProductKit\Block\Cart\CartItem::class,
            'kit-popup-product-' . $productData['product_id'],
            [
                'data' => [
                    'product' => $currentProduct,
                ],
            ]
        );

        if (!empty($productData['error'])) {
            $block->setError($productData['error']);
        }

        if (!$this->_view->getLayout()->getBlock('product.price.render.default')) {
            $this->_view->getLayout()->addBlock($layout->getBlock('product.price.render.default'));
        }

        $layout->unsetElement('product.info.addtocart');
        $layout->unsetElement('product.info.bundle.options.top');
        $layout->unsetElement('bundle.summary');

        $blockHtml = $block->toHtml();

        $blockHtml = str_replace(
            '[data-role=swatch-options]',
            '#cart_item_' . $productData['product_id'] . ' [data-role=swatch-options]',
            $blockHtml
        );
        $blockHtml = str_replace(
            'id="product-options-wrapper"',
            'id="product-options-wrapper-' . $productData['product_id'] . '"',
            $blockHtml
        );
        $blockHtml = str_replace(
            'id="super-product-table"',
            'id="super-product-table-' . $productData['product_id'] . '"',
            $blockHtml
        );
        $blockHtml = str_replace(
            '#super-product-table',
            '#super-product-table-' . $productData['product_id'],
            $blockHtml
        );
        // for configurable products with dropdown attribute
        $blockHtml = str_replace(
            'id="product_addtocart_form"',
            'id="product_addtocart_form-' . $productData['product_id'] . '"',
            $blockHtml
        );
        $blockHtml = str_replace(
            '#product_addtocart_form',
            '#product_addtocart_form-' . $productData['product_id'],
            $blockHtml
        );

        return $blockHtml;
    }
}
