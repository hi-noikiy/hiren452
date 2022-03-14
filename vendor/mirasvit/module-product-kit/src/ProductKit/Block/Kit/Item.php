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



namespace Mirasvit\ProductKit\Block\Kit;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Reports\Block\Product\AbstractProduct;
use Magento\Reports\Model\Product\Index\Factory as IndexFactory;
use Mirasvit\ProductKit\Data\OfferKitItem;

class Item extends AbstractProduct
{
    protected $_template = 'Mirasvit_ProductKit::kit/item.phtml';

    /**
     * @var OfferKitItem
     */
    private $item;

    private $priceCurrency;

    private $productRepository;

    public function __construct(
        ProductRepository $productRepository,
        PriceCurrencyInterface $priceCurrency,
        Visibility $productVisibility,
        IndexFactory $indexFactory,
        Context $context
    ) {
        $this->productRepository = $productRepository;
        $this->priceCurrency     = $priceCurrency;

        parent::__construct($context, $productVisibility, $indexFactory);
    }

    public function setItem(OfferKitItem $item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return OfferKitItem
     */
    public function getItem()
    {
        return $this->item;
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

    public function getItemOriginalPriceHtml(OfferKitItem $item)
    {
        return $this->priceCurrency->format($item->getFinalPrice());
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
}
