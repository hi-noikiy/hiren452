<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Mediaappearance
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Mediaappearance\Controller\Adminhtml\Mediaappearance;

class ProductsGrid extends \Magento\Catalog\Controller\Adminhtml\Category
{
      /**
       * @var \Magento\Framework\Controller\Result\RawFactory
       */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;


    /**
     * @param \Magento\Backend\App\Action\Context             $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory           $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory    = $layoutFactory;
    }//end __construct()


    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $category = $this->_initCategory(true);
        if (!$category) {
            /*
                @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
            */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('mediaappearanceadmin/*/', ['_current' => true, 'id' => null]);
        }

        /*
            @var \Magento\Framework\Controller\Result\Raw $resultRaw
        */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'FME\Mediaappearance\Block\Adminhtml\Mediaappearance\Edit\Tab\Products',
                'category.product.grid'
            )->toHtml()
        );
    }//end execute()
}
