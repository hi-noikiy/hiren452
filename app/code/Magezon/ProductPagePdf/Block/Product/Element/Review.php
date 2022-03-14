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

class Review extends \Magezon\ProductPagePdf\Block\Product\Element
{
    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magezon\ProductPagePdf\Model\Review\ReviewSummaryFactory
     */
    protected $reviewSummaryFactory;

    /**
     * @var \Magezon\ProductPagePdf\Helper\Data
     */
    protected $dataHelper;

    /**
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     */
    protected $reviewCollection;

    /**
     * @var int
     */
    protected $reviewsCount;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param \Magezon\ProductPagePdf\Model\Review\ReviewSummaryFactory $reviewSummaryFactory
     * @param \Magezon\ProductPagePdf\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        \Magezon\ProductPagePdf\Model\Review\ReviewSummaryFactory $reviewSummaryFactory,
        \Magezon\ProductPagePdf\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->collectionFactory = $collectionFactory;
        $this->reviewSummaryFactory = $reviewSummaryFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this->getCollection()->count()) {
            return parent::isEnabled();
        } 
        return false;
    }

    /**
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     */
    public function getCollection()
    {
        if ($this->reviewCollection == null) {
            $collection = $this->collectionFactory->create()
                ->addStatusFilter(
                    \Magento\Review\Model\Review::STATUS_APPROVED
                )->addEntityFilter(
                    'product',
                    $this->getProductId()
                )->addRateVotes()
                ->addFieldToSelect('*')
                ->setDateOrder();
                
            $this->reviewCollection = $collection;
        }
        return $this->reviewCollection;
    }

    /**
     * @return string
     */
    public function getVoteSrc($vote) 
    {
        $ratingSummary = $vote->getPercent();
        $imgLoad = '/app/code/Magezon/ProductPagePdf/view/frontend/web/images/' . $ratingSummary . '.png';
        return $this->dataHelper->getRootPath() . $imgLoad;
    }

    /**
     * @return int
     */
    public function getReviewsCount()
    {
        if ($this->reviewsCount == null) {
            $product = $this->getProduct();
            $this->reviewSummaryFactory->create()->appendSummaryDataToObject(
                $product,
                $product->getStoreId()
            );
            $this->reviewsCount = $product->getReviewsCount();
        }
        return $this->reviewsCount;
    }

    /**
     * @param string $date
     * @return string
     */
    public function formatReviewCreatDate($date) 
    {
        $date = substr($date, 0, 10);
        $month = substr($date, 5, 2);

        switch ($month) {
            case '01':
                $month = __('January');
                break;
            case '02':
                $month = __('February');
                break;
            case '03':
                $month = __('March');
                break;
            case '04':
                $month = __('April');
                break;
            case '05':
                $month = __('May');
                break;
            case '06':
                $month = __('June');
                break;
            case '07':
                $month = __('July');
                break;
            case '08':
                $month = __('August');
                break;
            case '09':
                $month = __('September');
                break;
            case '10':
                $month = __('October');
                break;
            case '11':
                $month = __('November');
                break;
            case '12':
                $month = __('December');
                break;
        }

        return $month . ' ' . substr($date, 8, 10) . ', ' . substr($date, 0, 4);
    }
}
