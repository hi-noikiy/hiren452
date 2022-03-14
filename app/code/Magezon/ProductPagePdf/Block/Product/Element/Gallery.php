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

class Gallery extends \Magezon\ProductPagePdf\Block\Product\Element
{
    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $mediaGalleryImages;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magezon\ProductPagePdf\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magezon\ProductPagePdf\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magezon\ProductPagePdf\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->dataHelper = $dataHelper;
    }
    
    /**
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this->getMediaGalleryImages()->count()) {
            return parent::isEnabled();
        }
        return false;
    }
    
    /**
     * @return \Magento\Framework\Data\Collection
     */
    public function getMediaGalleryImages()
    {
        if ($this->mediaGalleryImages == null) {
            $this->mediaGalleryImages = $this->getProduct()->getMediaGalleryImages();
        }
        return $this->mediaGalleryImages;
    }

    /**
     * @return array
     */
    public function getImagesConfigurable()
    {
        $product = $this->getProduct();
        $productIds = [];
        $images = [];
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $usedProducts = $product->getTypeInstance(true)->getUsedProducts($product);
            foreach ($usedProducts as $child) {
                $productIds[] = $child->getId();
            }
            $collection = $this->getProductConfigurableCollection($productIds);

            foreach($collection as $item) {
                if ($item->getImage()) {
                    $images[] = $this->dataHelper->getMediaPath() . "catalog/product" . $item->getImage();
                } else {
                    $imgLoad = '/app/code/Magezon/ProductPagePdf/view/frontend/web/images/thumbnail.jpg';
                    $images[] =  $this->dataHelper->getRootPath() . $imgLoad;
                }
            }

            if ($this->getElement()->getData('filter_same_img') && $images) { 
                $images = $this->filterSameImages(
                    $this->getMediaGalleryImages(),
                    $images
                );
            }
            return $images;
        }
    }

    /**
     * @param array $ids
     * @return \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public function getProductConfigurableCollection($ids)
    {
        return $this->productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id',['in' => $ids]);
    }

    /**
     * @param \Magento\Framework\Data\Collection $images
     * @param array $imagesChild
     * @return array
     */
    public function filterSameImages($images, $imagesChild) {
        $imagesChild = array_unique($imagesChild); 
        $arrs = [];
        foreach ($images as $image) {
            $path = $image->getPath();
            $strPath = substr((strstr($path,"product/")), 7,strlen($path));
            $arrs[] = $strPath;
        }
        foreach ($imagesChild as $key => $item) {
            foreach ($arrs as $element) {
                if ($element == $item) {
                    unset($imagesChild[$key]);
                }
            }
        }
        return $imagesChild;
    }
}
