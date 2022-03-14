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



namespace Mirasvit\ProductKit\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Reports\Block\Product\AbstractProduct;
use Magento\Reports\Model\Product\Index\Factory as IndexFactory;
use Mirasvit\ProductKit\Data\OfferKit;
use Mirasvit\ProductKit\Data\OfferKitItem;
use Mirasvit\ProductKit\Model\ConfigProvider;
use Mirasvit\ProductKit\Repository\KitRepository;
use Mirasvit\ProductKit\Service\OfferKitService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Kit extends AbstractProduct
{

    private $configProvider;

    private $kitRepository;

    private $localeFormat;

    private $offerKitService;

    /**
     * @var OfferKit
     */
    private $offerKit;

    private $productRepository;

    private $priceCurrency;

    public function __construct(
        ConfigProvider $configProvider,
        KitRepository $kitRepository,
        OfferKitService $offerKitService,
        LocaleFormat $localeFormat,
        ProductRepository $productRepository,
        PriceCurrencyInterface $priceCurrency,
        Context $context,
        Visibility $productVisibility,
        IndexFactory $indexFactory
    ) {
        $this->configProvider    = $configProvider;
        $this->kitRepository     = $kitRepository;
        $this->localeFormat      = $localeFormat;
        $this->offerKitService   = $offerKitService;
        $this->productRepository = $productRepository;
        $this->priceCurrency     = $priceCurrency;

        parent::__construct($context, $productVisibility, $indexFactory);
    }

    public function setOfferKit(OfferKit $offerKit)
    {
        $this->offerKit = $offerKit;

        return $this;
    }

    /**
     * @return OfferKit
     */
    public function getOfferKit()
    {
        return $this->offerKit;
    }

    /**
     * @return OfferKitItem[]
     */
    public function getItems()
    {
        $offerKit     = $this->getOfferKit();
        $combinations = $this->getOfferKit()->getCombinations();

        return isset($combinations[$offerKit->getMainCombinationHash()]) ?
            $combinations[$offerKit->getMainCombinationHash()] :
            $offerKit->getItems();
    }

    /**
     * @param OfferKitItem $item
     *
     * @return Product
     */
    public function getLinkedProduct(OfferKitItem $item)
    {
        return $this->productRepository->getById($item->getProductId());
    }

    public function getBaseId()
    {
        return 'mst-kit-' . $this->getOfferKit()->getId() . '-' . $this->getOfferKit()->getBlockId();
    }

    public function getJsonConfig()
    {
        $offerKit = $this->getOfferKit();

        $combinations = [];
        foreach ($offerKit->getCombinations() as $hash => $combination) {
            $combinations[$hash] = [
                'discounted_price' => $this->getOfferKit()->getKitPrice(OfferKit::KIT_PRICE_TYPE, $combination),
                'full_price'       => $this->getOfferKit()->getKitPrice(OfferKit::FINAL_PRICE_TYPE, $combination),
                'items'            => [],
            ];

            foreach ($combination as $offerKitItem) {
                $discountHtml = $this->configProvider->getIsShowDiscountText() ?
                    (string)__($this->configProvider->getDiscountText()) :
                    $this->getItemDiscountHtml($offerKitItem);

                $combinations[$hash]['items'][$offerKitItem->getId()] = [
                    OfferKitItem::ID              => $offerKitItem->getId(),
                    OfferKitItem::PRODUCT_ID      => $offerKitItem->getProductId(),
                    OfferKitItem::DISCOUNT_AMOUNT => $offerKitItem->getDiscountAmount(),
                    OfferKitItem::DISCOUNT_TYPE   => $offerKitItem->getDiscountType(),
                    OfferKitItem::POSITION        => $offerKitItem->getPosition(),
                    OfferKitItem::FINAL_PRICE     => $offerKitItem->getFinalPrice(),
                    OfferKitItem::KIT_PRICE       => $offerKitItem->getKitPrice(),
                    'discounted_price_html'       => $this->getItemPriceHtml($offerKitItem),
                    'discount'                    => $offerKitItem->getItemDiscount(),
                    'discount_html'               => $discountHtml,
                    'qty'                         => $offerKitItem->getQty(),
                ];
            }
        }

        $data = [
            '#' . $this->getBaseId() => [
                'Mirasvit_ProductKit/js/kit' => [
                    'base_id'         => $this->getBaseId(),
                    'kit_id'          => $this->getOfferKit()->getId(),
                    'title'           => $this->getOfferKit()->getTitle(),
                    'label'           => $this->getOfferKit()->getLabel(),
                    'combinations'    => $combinations,
                    'add_url'         => $this->getUrl('product_kit/cart/add'),
                    'action_name'     => $this->getRequest()->getFullActionName(),
                    'should_redirect' => $this->shouldRedirectToCart(),
                    'price_format'    => $this->localeFormat->getPriceFormat(),
                ],
            ],
        ];

        return $data;
    }

    /**
     * Is redirect should be performed after the product was added to cart.
     * @return bool
     */
    private function shouldRedirectToCart()
    {
        return $this->_scopeConfig->isSetFlag(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getItemDiscountHtml(OfferKitItem $item)
    {
        if ($item->getDiscountType() === ConfigProvider::DISCOUNT_TYPE_FIXED) {
            $baseCurrency = $this->_storeManager->getStore()->getBaseCurrency();

            return $this->priceCurrency->convertAndFormat(-1 * $item->getDiscountAmount(), true, 2, null, $baseCurrency);
        } else {
            return -1 * $item->getDiscountAmount() . '%';
        }
    }

    public function getItemOriginalPriceHtml(OfferKitItem $item)
    {
        $baseCurrency = $this->_storeManager->getStore()->getBaseCurrency();

        return $this->priceCurrency->convertAndFormat($item->getFinalPrice(), true, 2, null, $baseCurrency);
    }

    public function getItemPriceHtml(OfferKitItem $item)
    {
        $baseCurrency = $this->_storeManager->getStore()->getBaseCurrency();

        return $this->priceCurrency->convertAndFormat($item->getKitPrice(), true, 2, null, $baseCurrency);
    }

    public function isCurrentProduct(Product $product)
    {
        $contextProduct = $this->offerKitService->getContextProduct();

        return $contextProduct && $contextProduct->getId() === $product->getId();
    }

    /**
     * @param Product $product
     * @param string  $imageId
     *
     * @return string
     */
    public function getImageUrl(Product $product, $imageId = '')
    {
        // we use ObjectManager for compatibility with different Magento versions
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Helper\Image $imageHelper */
        $imageHelper = $objectManager->get('Magento\Catalog\Helper\Image');

        return $imageHelper->init($product, $imageId)->getUrl();
    }

    public function getItemHtml(OfferKitItem $item)
    {
        /** @var Kit\Item $block */
        $block = $this->_layout->createBlock(Kit\Item::class);

        $block->setItem($item);

        return $block->toHtml();
    }
}
