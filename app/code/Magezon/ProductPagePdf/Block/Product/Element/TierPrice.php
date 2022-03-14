<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductPagePdf\Block\Product\Element;

class TierPrice extends \Magezon\ProductPagePdf\Block\Product\Element
{
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->priceHelper = $priceHelper;
    }
    
    /**
     * @return boolean
     */
    public function isEnabled()
    {
        $product = $this->getProduct();
        if ($product->getTierPrices()) {
            return parent::isEnabled();
        }
        return false;
    }

    /**
     * @return string
     */
    public function getTierPricesHtml()
    {
        $product = $this->getProduct();
        $tierPrice = $product->getTierPrice();
        $strTierPrice = '';
        if ($tierPrice) {
            foreach ($tierPrice as $value) {
                $qty = (int)$value['price_qty'];
                $price = $value['price'];
                $formattedPrice = $this->priceHelper->currency($price, true, false);
                $savePercentageFormat = ceil(100 - ( (100 / $product->getPrice()) * $value['price']) ) . "%";
                $strTierPrice .= __("<p>Buy %1 for %2 each and save %3</p>", $qty, $formattedPrice, $savePercentageFormat);
            }
        }
        return $strTierPrice;
    }
}
