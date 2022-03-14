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



namespace Mirasvit\ProductKit\Data;

use Magento\Framework\DataObject;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Model\ConfigProvider;
use Mirasvit\ProductKit\Repository\KitItemRepository;
use Mirasvit\ProductKit\Service\KitBuilderService;
use Mirasvit\ProductKit\Service\PricePatternService;

class OfferKit extends DataObject
{
    const KIT           = 'kit';
    const ID            = 'kit_id';
    const BLOCK_ID      = 'block_id';
    const TITLE         = 'title';
    const LABEL         = 'label';
    const COMBINATIONS  = 'combinations';
    const ITEMS         = 'items';
    const PRICE_PATTERN = KitInterface::PRICE_PATTERN;

    const MAIN_COMBINATION_HASH = 'main_combination_hash';

    const KIT_PRICE_TYPE   = 'kit';
    const FINAL_PRICE_TYPE = 'final';

    private $pricePatternService;

    private $kitBuilderService;

    private $kitItemRepository;

    public function __construct(
        PricePatternService $pricePatternService,
        KitBuilderService $kitBuilderService,
        KitItemRepository $kitItemRepository,
        array $data = []
    ) {
        $this->pricePatternService = $pricePatternService;
        $this->kitBuilderService   = $kitBuilderService;
        $this->kitItemRepository   = $kitItemRepository;

        parent::__construct($data);
    }

    public function setKit(KitInterface $kit)
    {
        return $this->setData(self::ID, $kit->getId())
            ->setData(self::TITLE, $kit->getTitle())
            ->setData(self::PRICE_PATTERN, $kit->getPricePattern())
            ->setData(self::LABEL, $kit->getLabel())
            ->setData(self::KIT, $kit);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getBlockId()
    {
        return $this->getData(self::BLOCK_ID);
    }

    /**
     * @param int $value
     *
     * @return OfferKit
     */
    public function setBlockId($value)
    {
        return $this->setData(self::BLOCK_ID, $value);
    }

    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    public function getPricePattern()
    {
        return $this->getData(self::PRICE_PATTERN);
    }

    /**
     * @param string $type
     * @param array  $combination
     * @return int
     */
    public function getKitPrice($type, $combination)
    {
        $price = 0;

        foreach ($combination as $offerKitItem) {
            $itemPrice = $type == self::KIT_PRICE_TYPE
                ? $offerKitItem->getKitPrice()
                : $offerKitItem->getFinalPrice();

            $price += $itemPrice;
        }

        if ($type == self::KIT_PRICE_TYPE) {
            $price = $this->pricePatternService->template($this->getPricePattern(), $price);
        }

        return $price;
    }

    /**
     * @return OfferKitItem[]
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS);
    }

    public function setItems(array $items)
    {
        return $this->setData(self::ITEMS, $items);
    }

    /**
     * @return OfferKitItem[][]
     */
    public function getCombinations()
    {
        return $this->getData(self::COMBINATIONS);
    }

    public function setCombinations(array $combinations)
    {
        return $this->setData(self::COMBINATIONS, $combinations);
    }

    /**
     * @return string
     */
    public function getMainCombinationHash()
    {
        return $this->getData(self::MAIN_COMBINATION_HASH);
    }

    /**
     * @param string $hash
     * @return OfferKit
     */
    public function setMainCombinationHash($hash)
    {
        return $this->setData(self::MAIN_COMBINATION_HASH, $hash);
    }
}
