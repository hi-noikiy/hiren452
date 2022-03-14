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



namespace Mirasvit\ProductKit\Service;

use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Data\OfferKitItem;
use Mirasvit\ProductKit\Data\OfferKitItemFactory;
use Mirasvit\ProductKit\Model\ConfigProvider;

class KitBuilderService
{
    /**
     * ['position' => KitItemInterface]
     * @var KitItemInterface[]
     */
    private $items;

    private $offerKitItemFactory;

    public function __construct(
        OfferKitItemFactory $offerKitItemFactory
    ) {
        $this->offerKitItemFactory = $offerKitItemFactory;
    }

    /**
     * @param KitItemInterface[] $items
     *
     * @return KitItemInterface[][]
     */
    public function getItemCombinations(array $items)
    {
        $position     = 1;
        $combinations = [];

        $this->setKitItems($items);

        foreach ($items as $idx => $item) {
            if ($item->isPrimary()) {
                $newItems = $items;
                unset($newItems[$idx]);
                $newItems = array_values($newItems);

                $newItem = clone $item;

                if ($newItem->getDiscountType() == ConfigProvider::DISCOUNT_TYPE_PERCENTAGE_RELATIVE) {
                    $newItem->setDiscountType(ConfigProvider::DISCOUNT_TYPE_PERCENTAGE)
                        ->setDiscountAmount($this->items[$position]->getDiscountAmount());
                }

                $combination = [$newItem];

                $this->combine($combinations, $newItems, $combination, $position);
            }
        }

        $result = [];

        foreach ($combinations as $combination) {
            if (count($combination) > 1) {
                $result[] = $combination;
            }
        }

        return $result;
    }

    /**
     * @param KitItemInterface[] $items
     */
    private function setKitItems($items)
    {
        foreach ($items as $item) {
            $this->items[$item->getPosition()] = $item;
        }
    }

    /**
     * @param KitItemInterface[] $kitItems
     * @param KitItemInterface[] $combination
     *
     * @return OfferKitItem[]
     */
    public function getOfferItems(array $kitItems, $combination)
    {
        $offerItems = [];

        foreach ($combination as $index => $kitItem) {
            $offerItem = $this->offerKitItemFactory->create();
            $offerItem->setItem($kitItem);

            if ($offerItem->getDiscountType() == ConfigProvider::DISCOUNT_TYPE_PERCENTAGE_RELATIVE) {
                $offerItem->setDiscountType(ConfigProvider::DISCOUNT_TYPE_PERCENTAGE)
                    ->setDiscountAmount($kitItems[$index]->getDiscountAmount());
            }
            if ($offerItem->getDiscountType() == ConfigProvider::DISCOUNT_TYPE_PERCENTAGE_KIT) {
                $offerItem->setDiscountType(ConfigProvider::DISCOUNT_TYPE_PERCENTAGE)
                    ->setDiscountAmount($kitItems[count($combination)]->getDiscountAmount());
            }

            $offerItems[] = $offerItem;
        }

        return $offerItems;
    }

    /**
     * @param KitItemInterface[][] $combinations
     * @param KitItemInterface[]   $items
     * @param KitItemInterface[]   $combination
     * @param int                  $position
     */
    private function combine(array &$combinations, array $items, array $combination, $position)
    {
        if (count($items) == 0) {
            $combinations[] = $combination;

            return;
        }

        $newItems = $items;
        unset($newItems[0]);
        $newItems = array_values($newItems);

        $item = $items[0];

        if ($item->isOptional()) {
            $this->combine($combinations, $newItems, $combination, $position);
        }

        $position++;

        $newItem = clone $item;

        if ($newItem->getDiscountType() == ConfigProvider::DISCOUNT_TYPE_PERCENTAGE_RELATIVE) {
            $newItem->setDiscountType(ConfigProvider::DISCOUNT_TYPE_PERCENTAGE)
                ->setDiscountAmount($this->items[$position]->getDiscountAmount());
        }

        $nextCombination   = $combination;
        $nextCombination[] = $newItem;
        $this->combine($combinations, $newItems, $nextCombination, $position);
    }
}
