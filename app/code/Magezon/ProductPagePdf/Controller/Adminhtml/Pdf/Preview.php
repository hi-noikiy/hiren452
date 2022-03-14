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

namespace Magezon\ProductPagePdf\Controller\Adminhtml\Pdf;

class Preview extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_ProductPagePdf::profile';

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magezon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Magezon\ProductPagePdf\Model\PdfProcessor
     */
    protected $pdfProcess;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magezon\Core\Helper\Data $coreHelper
     * @param \Magezon\ProductPagePdf\Model\PdfProcessor $pdfProcess
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magezon\Core\Helper\Data $coreHelper,
        \Magezon\ProductPagePdf\Model\PdfProcessor $pdfProcess
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->coreHelper = $coreHelper;
        $this->pdfProcess = $pdfProcess;
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (isset($post['profile']) && isset($post['type']) && isset($post['id'])) {
            $product = $this->productFactory->create();
            $product->load($post['id']);
            $this->pdfProcess->setProduct($product);

            $options = [
                'orientation' => $post['pdf_orientation'],
                'format'      => $post['pdf_page_size']
            ];

            if (!$this->coreHelper->isNull($post['pdf_margin_top'])) {
                $options['margin_top'] = $this->getOption($post['pdf_margin_top']);
            }
            if (!$this->coreHelper->isNull($post['pdf_margin_right'])) {
                $options['margin_right'] = $this->getOption($post['pdf_margin_right']);
            }
            if (!$this->coreHelper->isNull($post['pdf_margin_bottom'])) {
                $options['margin_bottom'] = $this->getOption($post['pdf_margin_bottom']);
            }
            if (!$this->coreHelper->isNull($post['pdf_margin_left'])) {
                $options['margin_left'] = $this->getOption($post['pdf_margin_left']);
            }

            $mpdf = $this->pdfProcess->getPdf($post['profile'], $options);
            $mpdf->Output();
        }
        return;
    }

    /**
     * @param int $value
     * @return int|void
     */
    public function getOption ($value) {
        if ($value < 250 && $value >= 0) {
            return (float)$value;
        }
    }
}
