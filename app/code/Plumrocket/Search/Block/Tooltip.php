<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Search Autocomplete & Suggest
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Search\Block;

class Tooltip extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Plumrocket\Search\Helper\Data
     */
    private $helper;

    /**
     * @var \Plumrocket\Search\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    private $blockFactory;

    /**
     * @var \Plumrocket\Search\Model\Search\Result
     */
    private $searchResult;

    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    private $listProduct;

    /**
     * Tooltip constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\Search\Helper\Data                   $helper
     * @param \Plumrocket\Search\Helper\Config                 $config
     * @param \Plumrocket\Search\Model\Search\Result           $searchResult
     * @param \Magento\Framework\View\Element\BlockFactory     $blockFactory
     * @param \Magento\Catalog\Block\Product\ListProduct       $listProduct
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Search\Helper\Data $helper,
        \Plumrocket\Search\Helper\Config $config,
        \Plumrocket\Search\Model\Search\Result $searchResult,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->listProduct = $listProduct;
        $this->config = $config;
        $this->blockFactory = $blockFactory;
        $this->searchResult = $searchResult;

        parent::__construct($context, $data);
    }

    /**
     * @return \Plumrocket\Search\Model\ResourceModel\Result\Builder
     */
    public function getProducts()
    {
        return $this->searchResult->getProducts();
    }

    /**
     * @return array|bool|\Magento\Framework\DataObject[]
     */
    public function getCategories()
    {
        return $this->searchResult->getCategories();
    }

    /**
     * @return array
     */
    public function getCategoryIds()
    {
        return $this->searchResult->countForCategories();
    }

    /**
     * @param $category
     * @return int|mixed
     */
    public function getProductCount($category)
    {
        if ($this->helper->versionCompare()) {
            return $category->getProductCount();
        }

        $catIds = $this->getCategoryIds();
        $catId = $category->getId();

        return isset($catIds[$catId]) ? $catIds[$catId] : 0;
    }

    /**
     * @param $category
     * @return mixed
     */
    public function getParentCategory($category)
    {
        $parent = $category->getParentCategory();

        if ($parent && $parent->getLevel() > 1) { //it has not yet been verified
            return $parent;
        }
    }

    /**
     * @return \Magento\Search\Model\ResourceModel\Query\Collection
     */
    public function getTerms()
    {
        return $this->searchResult->getTerms();
    }

    /**
     * @return string
     */
    public function getProductCssClass()
    {
        $config = $this->config;

        $class = '';
        $class .= $config->showPSThumbs() ? '' : ' no-photo';
        $class .= $config->showPSPrice() ? '' : ' no-price';
        $class .= $config->showPSRating() ? '' : ' no-rating';
        $class .= $config->showPSShortDescription() ? '' : ' no-description';

        return $class;
    }

    /**
     * @param $text
     * @return mixed|null|string|string[]
     */
    public function tipsWords($text)
    {
        $helper = $this->helper;

        if ($words = $helper->splitWords()) {
            foreach ($words as &$word) {
                $word = preg_quote($this->escapeHtml($word), '/');
            }

            $text = str_replace('&amp;', '&', $text);
            $text = preg_replace(
                '/('. implode('|', $words) .')/iu',
                '(:TIP:)\0(:ENDTIP:)',
                $text
            );
            $text = $this->escapeHtml($text);
            $text = str_replace(['(:TIP:)', '(:ENDTIP:)'], ['<span class="psearch-tips">', '</span>'], $text);
        }

        return $text;
    }

    /**
     * @param  $product
     * @param  $imageType
     * @return mixed
     */
    public function getImageUrl($product, $imageType)
    {
        $imageBlock = $this->blockFactory->createBlock('Magento\Catalog\Block\Product\ListProduct');
        $productImage = $imageBlock->getImage($product, $imageType);
        $imageUrl = $productImage->getImageUrl();

        return $imageUrl;
    }

    /**
     * @param $shortDescription
     * @param $length
     * @param $etc
     * @param $remainder
     * @param $breakWords
     * @return string
     */
    public function getTruncate($shortDescription, $length, $etc, $remainder, $breakWords)
    {
        return $this->filterManager->truncate($shortDescription, [
            'length' => $length,
            $etc,
            $remainder,
            $breakWords
        ]);
    }

    /**
     * @param $product
     * @return string
     */
    public function getHtmlPrice($product)
    {
        return $this->listProduct->getProductPrice($product);
    }
}
