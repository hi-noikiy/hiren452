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



namespace Mirasvit\ProductKit\Plugin;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection;
use Magento\SalesRule\Model\RulesApplier;
use Mirasvit\ProductKit\Data\QuoteKitCollection;
use Mirasvit\ProductKit\Data\QuoteKitCollectionFactory;
use Mirasvit\ProductKit\Repository\KitRepository;
use Mirasvit\ProductKit\Service\CartService;
use Mirasvit\ProductKit\Service\PricePatternService;

/**
 * @see \Magento\SalesRule\Model\RulesApplier::applyRules
 */
class ApplyDiscountPlugin
{
    /**
     * @var array
     */
    public static $kits      = [];

    public static $discounts = [];

    private       $cartService;

    private       $kitRepository;

    private       $priceCurrency;

    private       $pricePatternService;

    private       $productMetadata;

    private       $quoteKitCollectionFactory;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        ProductMetadataInterface $productMetadata,
        PricePatternService $pricePatternService,
        QuoteKitCollectionFactory $quoteKitCollectionFactory,
        CartService $cartService,
        KitRepository $kitRepository
    ) {
        $this->cartService               = $cartService;
        $this->kitRepository             = $kitRepository;
        $this->priceCurrency             = $priceCurrency;
        $this->pricePatternService       = $pricePatternService;
        $this->productMetadata           = $productMetadata;
        $this->quoteKitCollectionFactory = $quoteKitCollectionFactory;
    }

    /**
     * @param RulesApplier $subject
     * @param callable     $proceed
     * @param AbstractItem $item
     * @param Collection   $rules
     * @param bool         $skipValidation
     * @param string       $couponCode
     *
     * @return array
     */
    public function aroundApplyRules($subject, $proceed, $item, $rules, $skipValidation, $couponCode)
    {
        $result = $proceed($item, $rules, $skipValidation, $couponCode);

        $requestOptions = $this->cartService->getItemOptions($item);
        if (!$requestOptions) {
            return $result;
        }

        $kitId = (int)$requestOptions['kit_id'];
        if (!$this->isValidKit($item, $requestOptions['kit_info']['hash'])) {
            return $result;
        }

        $kit = $this->kitRepository->get($kitId);

        $productId = $item->getProduct()->getId();
        if ($kit) {
            $discount = isset(self::$discounts[$kit->getId()][$productId]) ?
                self::$discounts[$kit->getId()][$productId] :
                0;

            $item->setDiscountAmount($discount);
            $item->setBaseDiscountAmount($discount);

            $this->applyBundleDiscount($item);

            $address     = $item->getAddress();
            $description = [
                'kit' => $kit->getLabel(),
            ];
            $address->setDiscountDescriptionArray($description);

            $result = []; // reset applied rules
        }

        return $result;
    }

    /**
     * We need this because in m2.4.0 method \Magento\SalesRule\Model\RulesApplier::distributeDiscount was removed
     *
     * @param AbstractItem $item
     *
     * @return void
     */
    private function applyBundleDiscount($item)
    {
        if (version_compare($this->productMetadata->getVersion(), "2.4.0", ">=") &&
            $item->getProduct()->getTypeId() == 'bundle'
        ) {
            $keys = [
                'discount_amount',
                'base_discount_amount',
                'original_discount_amount',
                'base_original_discount_amount',
            ];

            $roundingDelta      = [];
            $parentBaseRowTotal = $item->getBaseRowTotal();

            foreach ($keys as $key) {
                //Initialize the rounding delta to a tiny number to avoid floating point precision problem
                $roundingDelta[$key] = 0.0000001;
            }

            foreach ($item->getChildren() as $child) {
                $ratio = $parentBaseRowTotal != 0 ? $child->getBaseRowTotal() / $parentBaseRowTotal : 0;

                foreach ($keys as $key) {
                    if (!$item->hasData($key)) {
                        continue;
                    }

                    $value        = $item->getData($key) * $ratio;
                    $roundedValue = $this->priceCurrency->round($value + $roundingDelta[$key]);

                    $roundingDelta[$key] += $value - $roundedValue;

                    $child->setData($key, $roundedValue);
                }
            }
        }
    }

    /**
     * @param AbstractItem $item
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function loadQuoteKits(AbstractItem $item)
    {
        /** @var QuoteKitCollection $quoteKitCollection */
        $quoteKitCollection = $this->quoteKitCollectionFactory->create();

        $items = $item->getQuote()->getAllItems();
        /** @var AbstractItem $quoteItem */
        foreach ($items as $quoteItem) {
            $options = $this->cartService->getItemOptions($quoteItem);

            if (!$options) {
                continue;
            }

            $kitId     = $options['kit_id'];
            $hash      = $options['kit_info']['hash'];
            $itemId    = $options['kit_info']['item_id'];
            $position  = $options['kit_info']['position'];
            $productId = $this->getProductId($quoteItem);

            $selectedCombination = $options['kit_info']['selectedCombination'];

            $quoteKitCollection->update($options);

            if ($quoteKit = $quoteKitCollection->getQuoteKit($hash)) {
                $quoteKitItem = $quoteKit->getItem($position);

                if ($this->cartService->findKitIndexByProduct($kitId, $productId, $position) > 0) {
                    $quoteKitCollection->getQuoteKit($hash)->getItem($position)->setValid(true);
                }

                $kit     = $quoteKitCollection->getKit($kitId);
                $kitItem = $quoteKitCollection->getKitItem($kit, $position);

                if ($kitItem && !$quoteItem->getParentItem() && $quoteItem->getBaseRowTotal() > 0) {
                    $currentCombinationItems = $quoteKitCollection->getKitItemCombination($kit, $selectedCombination);

                    $quoteKitItem->setQuoteProductId($quoteItem->getProduct()->getId());
                    $quoteKitItem->setPrice($quoteItem->getBaseCalculationPrice() * $quoteItem->getQty());

                    $discount = 0;
                    if (isset($currentCombinationItems[$itemId])) {
                        $discount = $this->cartService->getDiscountAmount($currentCombinationItems[$itemId], $quoteItem);
                    }

                    $quoteKitItem->setDiscount($discount);
                }
            }
        }

        foreach ($quoteKitCollection->getQuoteKits() as $quoteKit) {
            self::$kits[$quoteKit->getHash()] = $quoteKit->validate();
        }

        $this->fixItemsDiscount($quoteKitCollection);
    }

    /**
     * @param AbstractItem $item
     *
     * @return int|mixed
     */
    private function getProductId(AbstractItem $item)
    {
        $productId = $item->getProduct()->getId();

        // for grouped products
        $groupedOptions = $this->cartService->getItemInfoOptions($item);
        if (isset($groupedOptions['super_product_config']) &&
            isset($groupedOptions['super_product_config']['product_id'])) {
            $productId = $groupedOptions['super_product_config']['product_id'];
        }

        return $productId;
    }

    /**
     * @param QuoteKitCollection $quoteKitCollection
     *
     * @return void
     */
    private function fixItemsDiscount($quoteKitCollection)
    {
        foreach ($quoteKitCollection->getQuoteKits() as $quoteKit) {
            $kitId         = $quoteKit->getKitId();
            $kit           = $quoteKitCollection->getKit($kitId);
            $quoteKitItems = $quoteKit->getItems();

            $totalPrice = $quoteKit->getPrice() - $quoteKit->getDiscount();
            $finalPrice = $this->pricePatternService->template($kit->getPricePattern(), $totalPrice);

            $appliedDiscount = 0;
            if ($totalPrice < $finalPrice) {
                $priceDif     = round($finalPrice - $totalPrice, 2);
                $discountStep = round($priceDif / count($quoteKitItems), 2);

                foreach ($quoteKitItems as $quoteKitItem) {
                    $appliedDiscount           += $discountStep;
                    $itemId                     = $quoteKitItem->getQuoteProductId();
                    $discounts[$kitId][$itemId] = $quoteKitItem->getDiscount() - $discountStep;
                }

                $itemId = end($quoteKitItems)->getQuoteProductId();
                // correct rounding errors
                if ($appliedDiscount > $priceDif) {
                    $discounts[$kitId][$itemId] += ($appliedDiscount - $priceDif);
                } else {
                    $discounts[$kitId][$itemId] -= $priceDif - $appliedDiscount;
                }
            } else {
                $priceDif     = $totalPrice - $finalPrice;
                $discountStep = round($priceDif / count($quoteKitItems), 2);

                foreach ($quoteKitItems as $quoteKitItem) {
                    $appliedDiscount           += $discountStep;
                    $itemId                     = $quoteKitItem->getQuoteProductId();
                    $discounts[$kitId][$itemId] = $quoteKitItem->getDiscount() + $discountStep;
                }

                $itemId = end($quoteKitItems)->getQuoteProductId();

                // correct rounding errors
                if ($appliedDiscount > $priceDif) {
                    $discounts[$kitId][$itemId] -= $appliedDiscount - $priceDif;
                } else {
                    $discounts[$kitId][$itemId] += $priceDif - $appliedDiscount;
                }
            }
        }

        self::$discounts = $discounts;
    }

    /**
     * @param AbstractItem $item
     * @param string       $hash
     *
     * @return mixed
     */
    private function isValidKit($item, $hash)
    {
        if (!isset(self::$kits[$hash])) {
            $this->loadQuoteKits($item);
        }

        return self::$kits[$hash];
    }
}
