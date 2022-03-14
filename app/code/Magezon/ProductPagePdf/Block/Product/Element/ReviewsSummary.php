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

class ReviewsSummary extends \Magezon\ProductPagePdf\Block\Product\Element
{
    /**
     * @var \Magezon\ProductPagePdf\Model\Review\ReviewSummaryFactory
     */
    protected $reviewSummaryFactory;

    /**
     * @var string
     */
    protected $ratingSummary;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magezon\ProductPagePdf\Model\Review\ReviewSummaryFactory $reviewSummaryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magezon\ProductPagePdf\Model\Review\ReviewSummaryFactory $reviewSummaryFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->reviewSummaryFactory = $reviewSummaryFactory;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this->getRatingSummary() != null) return parent::isEnabled();
        return false;
    }

    /**
     * @return string
     */
    public function getRatingSummary()
    {
        if ($this->ratingSummary == null) {
            $product = $this->getProduct();
            $this->reviewSummaryFactory->create()->appendSummaryDataToObject(
                $product,
                $product->getStoreId()
            );
            $this->ratingSummary = $product->getRatingSummary();
        }
        return $this->ratingSummary;
    }
}
