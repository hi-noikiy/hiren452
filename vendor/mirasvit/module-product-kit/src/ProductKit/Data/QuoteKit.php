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

class QuoteKit
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var int
     */
    private $kitId;

    /**
     * @var QuoteKitItem[]
     */
    private $items;

    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    public function getKitId()
    {
        return $this->kitId;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setKitId($id)
    {
        $this->kitId = $id;

        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param QuoteKitItem[] $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    public function addItem(QuoteKitItem $item)
    {
        $this->items[$item->getPosition()] = $item;

        return $this;
    }

    /**
     * @param int $position
     * @return bool|QuoteKitItem
     */
    public function getItem($position)
    {
        return isset($this->items[$position]) ? $this->items[$position] : false;
    }

    public function validate()
    {
        $isValid = true;
        foreach ($this->getItems() as $item) {
            if (!$item->isValid()) {
                $isValid = false;
            }
        }

        return $isValid;
    }

    public function getPrice()
    {
        $price = 0;
        foreach ($this->getItems() as $item) {
            $price += $item->getPrice();
        }

        return $price;
    }

    public function getDiscount()
    {
        $discount = 0;
        foreach ($this->getItems() as $item) {
            $discount += $item->getDiscount();
        }

        return $discount;
    }
}