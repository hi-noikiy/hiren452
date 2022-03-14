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



namespace Mirasvit\ProductKit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    const DISCOUNT_TYPE_FIXED                 = 'fixed';
    const DISCOUNT_TYPE_PERCENTAGE            = 'percentage';
    const DISCOUNT_TYPE_PERCENTAGE_RELATIVE   = 'percentage_relative';
    const DISCOUNT_TYPE_PERCENTAGE_KIT        = 'percentage_kit';
    const DISCOUNT_TYPE_COMPLEX               = 'complex';

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string[]
     */
    public function getDisplayOn()
    {
        return explode(',', $this->scopeConfig->getValue('product_kit/display/display_on'));
    }

    /**
     * @return int
     */
    public function getOfferKitsLimit()
    {
        return $this->scopeConfig->getValue('product_kit/display/kits_limit');
    }

    /**
     * @return int
     */
    public function getProductsPerPosition()
    {
        return $this->scopeConfig->getValue('product_kit/display/products_per_position');
    }

    /**
     * @return int
     */
    public function getIsShowDiscountText()
    {
        return $this->scopeConfig->getValue('product_kit/display/is_show_discount_text');
    }

    /**
     * @return int
     */
    public function getDiscountText()
    {
        return $this->scopeConfig->getValue('product_kit/display/discount_text');
    }

    /**
     * @return string
     */
    public function getLayoutRelativeContainer()
    {
        return $this->scopeConfig->getValue('product_kit/display/layout/relative_container');
    }

    /**
     * @return string
     */
    public function getLayoutRelativePosition()
    {
        return $this->scopeConfig->getValue('product_kit/display/layout/relative_position');
    }
}
