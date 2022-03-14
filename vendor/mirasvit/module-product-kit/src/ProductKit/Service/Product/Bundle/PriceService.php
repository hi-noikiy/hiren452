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



namespace Mirasvit\ProductKit\Service\Product\Bundle;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\ProductKit\Service\CurrencyService;

class PriceService
{
    /**
     * @var CurrencyService
     */
    private $currencyService;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        CurrencyService $currencyService
    ) {
        $this->currencyService = $currencyService;
        $this->priceCurrency   = $priceCurrency;
        $this->storeManager    = $storeManager;
    }

    /**
     * @param \Magento\Catalog\Model\Product $bundleProduct
     * @param int                            $qty
     *
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDisplayPrice($bundleProduct, $qty = 1)
    {
        /** @var \Magento\Bundle\Pricing\Price\BundleRegularPrice $regularPriceModel */
        $regularPriceModel   = $bundleProduct->getPriceInfo()->getPrice('regular_price');
        $minimalRegularPrice = $regularPriceModel->getMinimalPrice();

        /** @var \Magento\Bundle\Pricing\Price\FinalPrice $finalPriceModel */
        $finalPriceModel = $bundleProduct->getPriceInfo()->getPrice('final_price');
        $minimalPrice    = $finalPriceModel->getMinimalPrice();
        $price           = $minimalRegularPrice->getValue();

        if ($minimalPrice->getValue() < $minimalRegularPrice->getValue()) {
            $price = $minimalPrice->getValue();
        }

        $price = $this->convertPrice($price);

        return $price;
    }

    /**
     * @param float $price
     *
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function convertPrice($price)
    {
        $store         = $this->storeManager->getStore();
        $baseCurrency  = $store->getBaseCurrency();
        $priceCurrency = $this->priceCurrency->getCurrency();

        if ($priceCurrency->getCode() != $baseCurrency->getCode()) {
            $price = $this->currencyService->convertToCurrency($price, $priceCurrency, $baseCurrency, $store);
        } else {
            $price = $this->priceCurrency->round($price);
        }

        return $price;
    }
}
