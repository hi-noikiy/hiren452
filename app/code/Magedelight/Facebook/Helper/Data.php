<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Facebook
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Facebook\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magedelight\Facebook\Model\AttributemapFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\ProductFactory as ProductResourceFactory;
use Magento\Framework\Url as UrlHelper;
use Magedelight\Facebook\Model\CronhistoryFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magedelight\Facebook\Model\Cronhistory;
use Magedelight\Facebook\Logger\Logger;
use Magento\GroupedProduct\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class Data extends AbstractHelper 
{
    protected $headerdata = ['id','title','description','availability','condition','price','link','image_link',
                            'additional_image_link','color','item_group_id','google_product_category','pattern',
                            'product_type','sale_price','sale_price_effective_date','shipping','shipping_weight',
                            'size','custom_label_0','custom_label_1','custom_label_2','custom_label_3','custom_label_4'];
                        
    protected $optionalVal = ['brand','mpn','gtin'];
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     *
     * @var AttributemapFactory 
     */
    protected $attributemapFactory;
    
    /**
     *
     * @var ProductFactory 
     */
    protected $productFactory;
    /**
     *
     * @var BlockFactory 
     */
    protected $blockFactory;
    
    /**
     *
     * @var GalleryReadHandler 
     */
    protected $galleryReadHandler;

    /**
     *
     * @var CategoryCollection 
     */
    protected $categoryColl;
    
    /**
     *
     * @var ProductResourceFactory 
     */
    protected $productResourceFactory;
    
    /**
     *
     * @var UrlHelper 
     */
    protected $urlHelper;
    
    /**
     *
     * @var CronhistoryFactory 
     */
    protected $cronhistoryFactory;

    /**
     *
     * @var DateTime 
     */
    protected $date;
    
    /**
     *
     * @var Logger 
     */
    protected $logger;
    
    protected $errMessage = '';

    /**
     * 
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param AttributemapFactory $attributemapFactory
     * @param ProductFactory $productFactory
     * @param BlockFactory $blockFactory
     * @param GalleryReadHandler $galleryReadHandler
     * @param CategoryCollectionFactory $categoryCollFactory
     * @param ProductResourceFactory $productResourceFactory
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        AttributemapFactory $attributemapFactory,
        ProductFactory $productFactory,
        BlockFactory $blockFactory,
        GalleryReadHandler $galleryReadHandler,
        CategoryCollectionFactory $categoryCollFactory,
        ProductResourceFactory $productResourceFactory,
        UrlHelper $urlHelper,
        CronhistoryFactory $cronhistoryFactory,
        DateTime $date,
        Logger $logger,
        Status $productStatus    
    ) 
    {
        $this->storeManager = $storeManager;
        $this->attributemapFactory = $attributemapFactory;
        $this->productFactory = $productFactory;
        $this->blockFactory = $blockFactory;
        $this->galleryReadHandler = $galleryReadHandler;
        $this->categoryCollFactory = $categoryCollFactory;
        $this->productResourceFactory = $productResourceFactory;
        $this->urlHelper = $urlHelper;
        $this->cronhistoryFactory = $cronhistoryFactory;
        $this->date = $date;
        $this->logger = $logger;
        $this->productStatus = $productStatus;
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
    }
    
    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool)$this->getConfig('magedelight_facebook/general/enabled');
    }
    
    public function isCronEnabled()
    {
        return (bool)$this->getConfig('magedelight_facebook/general/enable_cron_schedule');
    }

    /**
     * @return string|array|int|boolean
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getDataHeader($type)
    {
        $attributeMapData = $this->attributemapFactory->create()
                                  ->getCollection()
                                  ->getData();
        $attributeMapFbData = array_column($attributeMapData, 'fb_attribute');
        $optionalVal = array_intersect($this->optionalVal,$attributeMapFbData);
        if(empty($optionalVal)){
            $this->saveHistory(__("Please Map required field one of (gtin, mpn, brand)."),$type,Cronhistory::FAILED);
            $this->setErrorMsg(__("Please Map required field one of (gtin, mpn, brand)."));
            return false;
        }
        else{
            $selectedOptVal = array_values($optionalVal)[0];
        }
        $this->headerdata[] = $selectedOptVal;
        return $this->headerdata;
    }
    
    public function getProductData($headerdata,$storeId,$type)
    {
        $productColl =  $this->productFactory->create()
                             ->getCollection()
                             ->addAttributeToSelect("*")
                             ->joinAttribute(
                                    'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner',$storeId
                            );
        $productColl->addStoreFilter($storeId);
        $productColl->addFieldToFilter('visibility',array('in'=>array(Visibility::VISIBILITY_BOTH)));
        
        $productColl->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $productColl->addFieldToFilter('is_allow_facebook_feed',Boolean::VALUE_YES);
        $productdata = [];
        $productArray = [];
        foreach ($productColl as $productModel) {
            if($productModel->getTypeId()== \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
               
                $childrenProd = $productModel->getTypeInstance()->getUsedProducts($productModel);
                foreach ($childrenProd as $childProdModel){
                    if(!in_array($childProdModel->getStatus(), $this->productStatus->getVisibleStatusIds())){
                        continue;
                    }
                    $childProd = $this->productFactory->create()
                                      ->load($childProdModel->getId());
                    $urlparam = [];
                    foreach ($headerdata as $header) {
                        $mapMageAttr = $this->getMapMageAttr($header);
                        if($header=='item_group_id'){
                            $productdata[$header] = $productModel->getSku();
                        }
                        else{
                            $childProdData = $childProd->getData();
                            if(isset($childProdData['color']) && $childProdData['color']!=''){
                                $productResourceModel = $this->productResourceFactory->create();
                                $attribute = $productResourceModel->getAttribute('color');
                                $colorId = $childProdData['color'];
                                $colorText = $attribute->getSource()->getOptionText($colorId);
                                $urlparam['color'] = $colorText;
                            }
                            if(isset($childProdData['size']) && $childProdData['size']!=''){
                                $productResourceModel = $this->productResourceFactory->create();
                                $attribute = $productResourceModel->getAttribute('size');
                                $sizeId = $childProdData['size'];
                                $sizeText = $attribute->getSource()->getOptionText($sizeId);
                                $urlparam['size'] = $sizeText;
                            }
                            $urlparam['type'] = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
                            $urlparam['parent'] = $productModel->getSku();
                            $productdata[$header] = $this->getProductValue($childProd,$header,$mapMageAttr,$storeId,$urlparam,"config");
                        }
                        if(in_array($header, $this->optionalVal)){
                            if($productdata[$header]==''){
                                $this->saveHistory(__("Please enter required field value for one of (gtin, mpn, brand). for product ") . $childProd->getName(),$type,Cronhistory::FAILED);
                                $this->setErrorMsg(__("Please enter required field value for one of (gtin, mpn, brand). for product ") . $childProd->getName());
                                return false;
                            }
                        }
                    }
                    $productArray[] = $productdata;
                }
            }
            else{
                foreach ($headerdata as $header) {
                    $mapMageAttr = $this->getMapMageAttr($header);
                    $productdata[$header] = $this->getProductValue($productModel,$header,$mapMageAttr,$storeId);
                }
                if(in_array($header, $this->optionalVal)){
                    if($productdata[$header]==''){
                        $this->saveHistory(__("Please enter required field value for one of (gtin, mpn, brand). for product ") . $productModel->getName(),$type,Cronhistory::FAILED);
                        $this->setErrorMsg(__("Please enter required field value for one of (gtin, mpn, brand). for product ") . $productModel->getName());
                        return false;
                    }
                }
                $productArray[] = $productdata;
            }
            $this->logger->info('Product ' .$productModel->getSku() . ' processed');
        }
        if(empty($productArray)){
            $this->saveHistory(__("No product available for Facebook Feed."),$type,Cronhistory::FAILED);
            $this->setErrorMsg(__("No product available for Facebook Feed."));
            return false;
        }
        return $productArray;
    }
    
    public function getMapMageAttr($header)
    {
        $attributeMapColl = $this->attributemapFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('fb_attribute',$header);
        if($attributeMapColl->getSize()>0){
            return $attributeMapColl->getFirstItem()->getMageAttribute();
        }
        return false;
    }
    
    protected function getProductValue($productModel,$header,$mapMageAttr,$storeId,$urlparam=[],$suffix='')
    {
        if ($header=='condition'){
            return "new"; // current development for only new ecommerce products
        }
        if ($header=='link'){
           $urlparams = [ '_scope' => $storeId, '_nosid' => true,'sku'=>$productModel->getSku()];
           if(!empty($urlparam)){
               $urlparams = array_merge($urlparams,$urlparam);
           }
           return $this->urlHelper->getUrl('md_facebook/feedaction/add', $urlparams);
        }
        if($mapMageAttr=='is_in_stock'){
            if($productModel->isInStock()){
                return "in stock";
            }
            else{
                return "out of stock";
            }
        }
        if($mapMageAttr=='price'){
            $this->storeManager->setCurrentStore($storeId);
            $currecycode = $this->storeManager->getStore()->getCurrentCurrencyCode();
            if ($productModel->getTypeId() == CustomOptions::PRODUCT_TYPE_GROUPED) {
                $priceArray = [];
                $assProds = $productModel->getTypeInstance(true)->getAssociatedProducts($productModel);            
                foreach ($assProds as $asschild) {
                    if ($asschild->getId() != $productModel->getId()) {
                          $priceArray[]= $asschild->getPrice();
                    }
                }
               
                return min($priceArray)." ". $currecycode;
            }
            elseif ($productModel->getTypeId() == ProductType::TYPE_BUNDLE) {
                return $productModel->getPriceInfo()->getPrice('regular_price')->getMinimalPrice()->getValue() ." ". $currecycode;
            }
            else {
              return $productModel->getPrice() ." ". $currecycode;
            }
            
        }
        if($mapMageAttr=='main_img_link'){
            return $this->getGallery($productModel,'main_image');
        }
        if($mapMageAttr=='additional_image'){
            return $this->getGallery($productModel,'additional_image');
        }
        if($mapMageAttr=='category'){
            return $this->getProdCategories($productModel);
        }
        if($header=='google_product_category'){
            return $this->getProdCategories($productModel);
        }
        if($mapMageAttr=='special_price'){
            return $this->getSalePrice($productModel,$storeId);
        }
        if($mapMageAttr=='special_date'){
            return $this->getSalePriceEffDate($productModel);
        }
        $productResourceModel = $this->productResourceFactory->create();
        $attribute = $productResourceModel->getAttribute($mapMageAttr);
        if($attribute){
            if($attribute->getFrontendInput()=='select'){
                if ($attribute->usesSource()) {
                    $optionId = $productModel->getData($mapMageAttr);
                    return  $option_Text = $attribute->getSource()->getOptionText($optionId);
                }
            } 
        }
        if($suffix!='' && $mapMageAttr=='sku'){
            return $productModel->getData($mapMageAttr) . "-" . $suffix; 
        }
        
        return $productModel->getData($mapMageAttr);
    }
    
    private function getImageUrl($product)
    {
        $imageBlock = $this->blockFactory->createBlock('Magento\Catalog\Block\Product\ListProduct');
        $productImage = $imageBlock->getImage($product, 'product_base_image');
        $imageUrl = $productImage->getImageUrl();
        return $imageUrl;
    }
    
    /** Add image gallery to $product */
    protected function getGallery($product,$imagetype)
    {
        $this->galleryReadHandler->execute($product);
        $images = $product->getMediaGalleryImages();
        $additionalimage = [];
        $additionalimages = '';
        foreach ($images as $image) {
            $additionalimage[] = $image->getUrl();
        }
        if($imagetype=='main_image'){
            if(!empty($additionalimage)){
                $mainimage = $additionalimage[0];
                return $mainimage;
            }
            else{
                return '';
            }
        }
        if(!empty($additionalimage)){
            unset($additionalimage[0]);
            if(!empty($additionalimage)){
                $additionalimages = implode(',', $additionalimage);
            }
        }
        return $additionalimages;
    }
    
    protected function getProdCategories($productModel)
    {
        
        $categoryIds = $productModel->getCategoryIds();
        $categories = $this->categoryCollFactory->create()
                                         ->addAttributeToSelect('*')
                                         ->addAttributeToFilter('entity_id', $categoryIds);
        $prodcategory = '';
        foreach ($categories as $category) {
            $prodcategory .= $category->getName() . " > ";
        }
        return trim($prodcategory,'> ');
    }
    
    protected function getSalePrice($productModel,$storeId)
    {
        if($productModel->getSpecialPrice()!=''){
            $this->storeManager->setCurrentStore($storeId);
            $currecycode = $this->storeManager->getStore()->getCurrentCurrencyCode();
            return $productModel->getSpecialPrice() ." ". $currecycode;
        }
        return $productModel->getSpecialPrice();
    }
    
    protected function getSalePriceEffDate($productModel)
    {
        $specialfromdate = str_replace(" ", "T", $productModel->getSpecialFromDate());
        $specialtodate   = str_replace(" ", "T", $productModel->getSpecialToDate());
        if($specialfromdate!='' && $specialtodate!=''){
            return $specialfromdate . "/" . $specialtodate;
        }
        else {
            return;
        }
    }
    
    public function saveHistory($message,$type,$status)
    {
        $cronHistoryModel = $this->cronhistoryFactory->create();
        $date = $this->date->gmtDate();
        $cronHistoryModel->setCronDate($date);
        $cronHistoryModel->setMessage($message);
        $cronHistoryModel->setType($type);
        $cronHistoryModel->setStatus($status);
        $cronHistoryModel->save();
    }
    
    public function setErrorMsg($msg)
    {
        $this->errMessage = $msg;
    }
    
    public function getErrorMsg()
    {
        return $this->errMessage;
    }
    public function getExtensionKey()
    {
        return 'ek-facebook-shop-m2';
    }

    public function getExtensionDisplayName()
    {
        return 'Facebook Shop';
    }
}

