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
 * @package   mirasvit/module-product-action
 * @version   1.0.9
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\ProductAction\Action\LinkProducts\Service;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory as ProductLinkFactory;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Mirasvit\ProductAction\Api\LinkActionDataInterface;
use Magento\PageCache\Model\Cache\Type as PageCache;

class LinkProductsService
{
    private $pageCache;

    private $linkTypes = [
        1 => 'related',
        2 => 'upsell',
        3 => 'crosssell',
    ];

    private $productLinkFactory;

    private $productLinkRepository;

    private $productRepository;

    public function __construct(
        PageCache $pageCache,
        ProductLinkFactory $productLinkFactory,
        ProductLinkRepositoryInterface $productLinkRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->pageCache             = $pageCache;
        $this->productLinkFactory    = $productLinkFactory;
        $this->productLinkRepository = $productLinkRepository;
        $this->productRepository     = $productRepository;
    }

    public function link(LinkActionDataInterface $linkActionData)
    {
        $linkTypeName = $this->getLinkTypeName($linkActionData);

        foreach ($linkActionData->getIds() as $productId) {
            $product = $this->productRepository->getById($productId, true);

            $lastPosition = $this->getLastPosition($product, $linkTypeName);

            $this->addProducts($product, $linkActionData->getAddProductIds(), $linkTypeName, $lastPosition);
            $this->removeProducts($product, $linkActionData->getRemoveProductIds(), $linkTypeName);
            $this->copyProducts($product, $linkActionData->getCopyProductIds(), $linkTypeName, $lastPosition);

            if ($linkActionData->getRemoveAll()) {
                $this->removeAllProducts($product, $linkTypeName, $lastPosition);
            }

            $product->setProductLinks($this->productLinkRepository->getList($product));
            $this->pageCache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, $product->getCacheTags());

            if ($linkActionData->getDirection()) {
                foreach ($linkActionData->getAddProductIds() as $sku) {
                    $linkProduct  = $this->productRepository->get($sku);
                    $lastPosition = $this->getLastPosition($linkProduct, $linkTypeName);

                    $this->addProducts($linkProduct, [$product->getSku()], $linkTypeName, $lastPosition);

                    $linkProduct->setProductLinks($this->productLinkRepository->getList($linkProduct));
                    $this->pageCache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, $linkProduct->getCacheTags());
                }

                foreach ($linkActionData->getRemoveProductIds() as $sku) {
                    $linkProduct = $this->productRepository->get($sku);

                    $this->removeProducts($linkProduct, [$product->getSku()], $linkTypeName);

                    $linkProduct->setProductLinks($this->productLinkRepository->getList($linkProduct));
                    $this->pageCache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, $linkProduct->getCacheTags());
                }

                foreach ($linkActionData->getCopyProductIds() as $sku) {
                    $copyFromProduct = $this->productRepository->get($sku);
                    $lastPosition    = $this->getLastPosition($copyFromProduct, $linkTypeName);

                    $this->copyProducts($copyFromProduct, [$product->getSku()], $linkTypeName, $lastPosition);
                    $this->pageCache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, $copyFromProduct->getCacheTags());
                }
            }
        }
    }

    private function addProducts(ProductInterface $product, array $addProductSkus, string $linkTypeName, int $lastPosition): void
    {
        foreach ($addProductSkus as $sku) {
            $linkProduct = $this->productRepository->get($sku);
            $link        = $this->productLinkFactory->create();
            $link->setSku($product->getSku())
                ->setLinkedProductSku($linkProduct->getSku())
                ->setLinkType($linkTypeName)
                ->setPosition(++$lastPosition);

            $this->productLinkRepository->save($link);
        }
    }

    private function removeProducts(ProductInterface $product, array $removeProductSkus, string $linkTypeName): void
    {
        foreach ($removeProductSkus as $sku) {
            $this->productLinkRepository->deleteById($product->getSku(), $linkTypeName, $sku);
        }
    }

    private function copyProducts(ProductInterface $product, array $fromProductSkus, string $linkTypeName, int $lastPosition): void
    {
        foreach ($fromProductSkus as $sku) {
            $copyFromProduct = $this->productRepository->get($sku);
            $linkedProducts  = $copyFromProduct->getProductLinks();

            $linkSkus = [];
            foreach ($linkedProducts as $linkedProduct) {
                if ($linkTypeName == $linkedProduct->getLinkType()) {
                    $linkSkus[] = $linkedProduct->getLinkedProductSku();
                }
            }

            if ($linkSkus) {
                $this->addProducts($product, $linkSkus, $linkTypeName, $lastPosition);
            }
        }
    }

    private function removeAllProducts(ProductInterface $product, string $linkTypeName, int $lastPosition): void
    {
        $productLinks = $product->getProductLinks();
        foreach ($productLinks as $productLink) {
            try {
                if ($linkTypeName == $productLink->getLinkType()) {
                    $this->productLinkRepository->deleteById($product->getSku(), $linkTypeName, $productLink->getLinkedProductSku());
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    private function getLinkTypeName(LinkActionDataInterface $linkActionData): string
    {
        return $this->linkTypes[$linkActionData->getLinkType()];
    }

    private function getLastPosition(ProductInterface $product, string $linkType)
    {
        // Build links per type
        /** @var ProductLinkInterface[][] $linksByType */
        $linksByType = [];
        foreach ($product->getProductLinks() as $link) {
            $linksByType[$link->getLinkType()][] = $link;
        }

        return isset($linksByType[$linkType]) ? count($linksByType[$linkType]) : 0;
    }
}
