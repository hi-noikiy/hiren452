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

namespace Magezon\ProductPagePdf\Block\Product;

use Magento\Framework\View\Element\Template;

class Profile extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magezon\ProductPagePdf\Model\PdfProcessor
     */
    protected $pdfProcessor;

    /**
     * @var \Magezon\ProductPagePdf\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magezon\ProductPagePdf\Model\PdfProcessor $pdfProcessor
     * @param \Magezon\ProductPagePdf\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magezon\ProductPagePdf\Model\PdfProcessor $pdfProcessor,
        \Magezon\ProductPagePdf\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->pdfProcessor = $pdfProcessor;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->dataHelper->isAllowProduct()) {
            $blockType = $this->getData('block_type');
            $profile = $this->getProfile();
            if ($profile->getId()) {
                $buttonPosition = $profile->getButtonPosition();
                if ($blockType == $buttonPosition) 
                return parent::toHtml();
            }
        }
    }

    /**
     * @return \Magezon\ProductPagePdf\Model\Profile
     */
    public function getProfile()
    {
        return $this->pdfProcessor->getProfile();
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('productpagepdf/product/printaction', [
            'product_id' => $this->getProduct()->getId(),
            'product_id' => $this->getProduct()->getId()
        ]);
    }
}
