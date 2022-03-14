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

class QuoteKitItem extends DataObject
{
    private $valid          = false;

    private $position       = 0;

    private $price          = .0;

    private $discount       = .0;

    private $quoteProductId = 0;

    public function getQuoteProductId()
    {
        return $this->quoteProductId;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setQuoteProductId($value)
    {
        $this->quoteProductId = $value;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setPosition($value)
    {
        $this->position = $value;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setPrice($value)
    {
        $this->price = $value;

        return $this;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setDiscount($value)
    {
        $this->discount = $value;

        return $this;
    }

    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setValid($value)
    {
        $this->valid = $value;

        return $this;
    }
}
