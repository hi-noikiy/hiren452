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



namespace Mirasvit\ProductKit\Block\Cart;

use Magento\Catalog\Block\Product\View;

class CartItems extends View
{
    private $errors = [];

    /**
     * @var string[]
     */
    private $productBlocksHtml = [];

    public function _construct()
    {
        $this->setTemplate('Mirasvit_ProductKit::cart/cart-items.phtml');

        parent::_construct();
    }

    /**
     * @param string[] $html
     * @return $this
     */
    public function setProductBlocksHtml($html)
    {
        $this->productBlocksHtml = $html;

        return $this;
    }

    public function getProductBlocksHTml()
    {
        return $this->productBlocksHtml;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
