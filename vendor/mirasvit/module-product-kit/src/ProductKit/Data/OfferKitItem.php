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

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\DataObject;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Model\ConfigProvider;

class OfferKitItem extends DataObject
{
    const ID              = 'item_id';
    const PRODUCT_ID      = 'product_id';
    const DISCOUNT_TYPE   = 'discount_type';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const REGULAR_PRICE   = 'regular_price';
    const FINAL_PRICE     = 'final_price';
    const KIT_PRICE       = 'kit_price';
    const IS_OPTIONAL     = 'is_optional';
    const POSITION        = 'position';
    const QTY             = 'qty';
    const ITEM_VARIATIONS = 'item_variations';
    const IS_VISIBLE      = 'is_visible';

    private $productRepository;

    public function __construct(
        ProductRepository $productRepository,
        array $data = []
    ) {
        parent::__construct($data);

        $this->productRepository = $productRepository;
    }

    public function setItem(KitItemInterface $item)
    {
        return $this->setData(self::ID, $item->getId())
            ->setData(self::DISCOUNT_TYPE, $item->getDiscountType())
            ->setData(self::DISCOUNT_AMOUNT, $item->getDiscountAmount())
            ->setData(self::IS_OPTIONAL, $item->isOptional())
            ->setData(self::POSITION, $item->getPosition())
            ->setData(self::QTY, $item->getQty());
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * @param int $value
     *
     * @return OfferKitItem
     */
    public function setProductId($value)
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * @param float $value
     * @return OfferKitItem
     */
    public function setDiscountAmount($value)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $value);
    }

    public function getDiscountType()
    {
        return $this->getData(self::DISCOUNT_TYPE);
    }

    /**
     * @param string $value
     * @return OfferKitItem
     */
    public function setDiscountType($value)
    {
        return $this->setData(self::DISCOUNT_TYPE, $value);
    }

    public function getRegularPrice()
    {
        return $this->getData(self::REGULAR_PRICE);
    }

    /**
     * @param float $value
     *
     * @return OfferKitItem
     */
    public function setRegularPrice($value)
    {
        return $this->setData(self::REGULAR_PRICE, $value);
    }

    public function getFinalPrice()
    {
        return $this->getData(self::FINAL_PRICE);
    }

    /**
     * @param float $value
     *
     * @return OfferKitItem
     */
    public function setFinalPrice($value)
    {
        return $this->setData(self::FINAL_PRICE, $value);
    }

    /**
     * @return OfferKitItem[]
     */
    public function getItemVariations()
    {
        return (array)$this->getData(self::ITEM_VARIATIONS);
    }

    /**
     * @param OfferKitItem[] $set
     *
     * @return $this
     */
    public function setItemVariations($set)
    {
        return $this->setData(self::ITEM_VARIATIONS, $set);
    }

    public function getKitPrice()
    {
        if ($this->getDiscountType() === ConfigProvider::DISCOUNT_TYPE_FIXED) {
            $kitPrice = $this->getFinalPrice() - $this->getDiscountAmount();
        } else {
            $kitPrice = $this->getFinalPrice() - ($this->getFinalPrice() * $this->getDiscountAmount() / 100);
        }

        return $kitPrice;
    }

    public function getItemDiscount()
    {
        if ($this->getDiscountType() === ConfigProvider::DISCOUNT_TYPE_FIXED) {
            $itemDiscount = $this->getDiscountAmount();
        } else {
            $itemDiscount = $this->getFinalPrice() * $this->getDiscountAmount() / 100;
        }

        return $itemDiscount;
    }

    /**
     * @param float $value
     *
     * @return OfferKitItem
     */
    public function setKitPrice($value)
    {
        return $this->setData(self::KIT_PRICE, $value);
    }

    public function isOptional()
    {
        return $this->getData(self::IS_OPTIONAL);
    }

    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @param bool $isVisible
     *
     * @return OfferKitItem
     */
    public function setIsVisible($isVisible)
    {
        return $this->setData(self::IS_VISIBLE, $isVisible);
    }

    public function getIsVisible()
    {
        return (bool)$this->getData(self::IS_VISIBLE);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct()
    {
        return $this->productRepository->getById($this->getProductId());
    }
}
