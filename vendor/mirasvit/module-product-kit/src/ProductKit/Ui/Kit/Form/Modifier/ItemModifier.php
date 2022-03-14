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



namespace Mirasvit\ProductKit\Ui\Kit\Form\Modifier;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status as StatusSource;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Repository\KitItemRepository;

class ItemModifier
{
    private $currency;

    private $itemRepository;

    private $imageHelper;

    private $priceHelper;

    private $productRepository;

    private $statusSource;

    public function __construct(
        KitItemRepository $itemRepository,
        ProductRepository $productRepository,
        ImageHelper $imageHelper,
        StatusSource $statusSource,
        CurrencyInterface $currency,
        PriceHelper $priceHelper
    ) {
        $this->itemRepository    = $itemRepository;
        $this->productRepository = $productRepository;
        $this->imageHelper       = $imageHelper;
        $this->statusSource      = $statusSource;
        $this->currency          = $currency;
        $this->priceHelper       = $priceHelper;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    public function modifyData(KitInterface $kit, array $data)
    {
        $data['links']['product_kit_kit_form_product_listing'] = $this->buildData($data[KitInterface::ID]);

        return $data;
    }

    /**
     * @param int $kitId
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Currency_Exception
     */
    private function buildData($kitId)
    {
        $items = $this->itemRepository->getCollection();
        $items->addFieldToFilter(KitItemInterface::KIT_ID, $kitId)
            ->setOrder(KitItemInterface::POSITION, 'asc');

        $result = [];

        foreach ($items as $item) {
            $product = $this->productRepository->getById($item->getProductId());

            $result[] = [
                'id'                              => $product->getId(),
                'sku'                             => $product->getSku(),
                'name'                            => $product->getName(),
                'status'                          => $this->statusSource->getOptionText($product->getStatus()),
                'thumbnail'                       => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
                'price'                           => $this->priceHelper->currency($product->getPrice(), true, false),
                KitItemInterface::IS_OPTIONAL     => $item->isOptional() ? "1" : "0",
                KitItemInterface::IS_PRIMARY      => $item->isPrimary() ? "1" : "0",
                KitItemInterface::QTY             => $item->getQty(),
                KitItemInterface::DISCOUNT_AMOUNT => round($item->getDiscountAmount(), 2),
                KitItemInterface::DISCOUNT_TYPE   => $item->getDiscountType(),
                KitItemInterface::POSITION        => $item->getPosition(),
            ];
        }

        return $result;
    }
}
