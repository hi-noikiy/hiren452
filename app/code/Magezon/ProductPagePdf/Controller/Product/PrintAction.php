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

namespace Magezon\ProductPagePdf\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;

class PrintAction extends Action
{
    /**
     * @var \Magezon\ProductPagePdf\Model\PdfProcessor
     */
    protected $pdfProcessor;

    /**
     * @param Context $context
     * @param \Magezon\ProductPagePdf\Model\PdfProcessor $pdfProcessor
     */
    public function __construct(
        Context $context,
        \Magezon\ProductPagePdf\Model\PdfProcessor $pdfProcessor
    ) {
        parent::__construct($context);
        $this->pdfProcessor = $pdfProcessor;
    }

    /**
     * @return void
     */
    public function execute()
    {
        try {
            $productId = (int) $this->getRequest()->getParam('product_id');
            $pdfProcessor = $this->pdfProcessor;
            $mpdf = $pdfProcessor->getProductPdf($productId);
            if ($mpdf) {
                $mpdf->Output($pdfProcessor->getFilename(), $pdfProcessor->getDestination());
                $this->getResponse()->setHeader(
                    'Cache-Control',
                    'must-revalidate, post-check=0, pre-check=0',
                    true
                );
                return;
            }
        } catch (\Exception $e) { }
    }
}
