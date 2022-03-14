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
namespace FME\Mediaappearance\Block;

use Magento\Store\Model\Store;
//DataUpgrade
error_reporting(E_ALL); 
ini_set('display_errors', 1);
class Mediaappearance extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $_scopeConfig;

    /**
     * @var \FME\Mediaappearance\Helper\Data
     */
    public $_helper;
    protected $_itemsLimit;

    /**
     * @var $_pages Number of Pages
     */
    protected $jsLayout;
    protected $request;
    /**
     *
     * @param \Magento\Backend\Block\Template\Context                               $context
     * @param \Magento\Framework\ObjectManagerInterface                             $objectManager
     * @param \Magento\Framework\Registry                                           $registry
     * @param \Magento\Store\Model\StoreManagerInterface                            $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                    $scopeConfig
     * @param \Magento\Framework\UrlInterface                                       $urlInterface
     * @param \FME\Mediaappearance\Helper\Data                                      $helper
     * @param \Magento\Catalog\Model\Product                                        $product
     * @param \Magento\Catalog\Model\ProductFactory                                 $productFactory
     * @param \FME\Mediaappearance\Model\Resource\Mediaappearance\CollectionFactory $mediaappearancemediaappearanceFactory
     * @param \FME\Mediaappearance\Model\Mediaappearance                            $mediaappearancemediaappearance
     * @param \Magento\Framework\View\Page\Config                                   $pageConfig
     * @param \Magento\Framework\App\ResourceFactory                                $coreresourceFactory
     * @param \Magento\Framework\App\Resource                                       $coreresource
     * @param array                                                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        \FME\Mediaappearance\Helper\Data $helper,
        \FME\Mediaappearance\Helper\DataUpgrade $helperupgrade,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \FME\Mediaappearance\Model\ResourceModel\Mediaappearance\CollectionFactory $mediaappearancemediaappearanceFactory,
        \FME\Mediaappearance\Model\ResourceModel\Media\CollectionFactory $mediaappearanceFactory,
        \FME\Mediaappearance\Model\Media $mediaappearancemediaappearance,
        \Magento\Framework\App\ResourceConnectionFactory $coreresourceFactory,
        \Magento\Framework\App\ResourceConnection $coreresource,
        array $data = []
    ) {

        $this->_mediaappearancemediaappearanceFactory = $mediaappearancemediaappearanceFactory;
        $this->_mediaappearancemediaappearance = $mediaappearancemediaappearance;
        $this->mediaappearanceFactory=$mediaappearanceFactory;
        $this->_product = $product;
        $this->_productFactory = $productFactory;
        $this->_urlInterface = $context->getUrlBuilder();
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $context->getStoreManager();
        $this->pageConfig = $context->getPageConfig();
        $this->_helper = $helper;
        $this->_helperupgrade = $helperupgrade;
        $this->request = $request;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_coreresourceFactory = $coreresourceFactory;
        $this->_coreresource = $coreresource;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        parent::__construct($context, $data);
    }

    /**
     * @return Layout
     */
    protected function _construct()
    {
        parent::_construct();
        $title = $this->_helper->getPageTitle();
        $this->pageConfig->getTitle()->set(__($title));
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');

        $metaKeywords = $this->_helper->getMetaKeywords();
        $metaDescription = $this->_helper->getMetaDesp();

        $this->pageConfig->setKeywords($metaKeywords);
        $this->pageConfig->setDescription($metaDescription);

        $breadcrumbs->addCrumb('home', [
            'label' => __('Home'),
            'title' => __('Go to Home Page'),
            'link' => $this->_storeManager->getStore()->getBaseUrl()]);

        $breadcrumbs->addCrumb('mediaappearance', [
            'label' => __($title),
            'title' => __($title),
            'link' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        return \Zend_Json::encode($this->jsLayout);
    }
    public function getParameters()
    {
        //print_r($this->request->getParam('id'));exit;
        return $this->request->getParam('id');
    }
    /**
     * function getMediaappearance
     * @return array
     */
    public function getMediaAllCollectionVideos()
    {

        $media=[];
       $maincollection=$this->getMediaappearance();
       foreach ($maincollection as $_gal) {
            $media[]=$this->_mediaappearancemediaappearance->getRelatedMediaOnDrop($_gal['mediagallery_id'],"video");
        }
       
        return $media;
    }
    public function getMediaAllCollectionImages()
    {

        $media=[];
       $maincollection=$this->getMediaappearance();
       foreach ($maincollection as $_gal) {
            $media[]=$this->_mediaappearancemediaappearance->getRelatedMediaOnDrop($_gal['mediagallery_id'],"images");
        }
       
        return $media;
    }
    public function getMediaAllCollection()
    {

        $media=[];
       $maincollection=$this->getMediaappearance();
       foreach ($maincollection as $_gal) {
            $media[]=$this->_mediaappearancemediaappearance->getRelatedMediaOnDrop($_gal['mediagallery_id'],"");
        }
        return $media;
       }
    public function getMediaappearance()
    {
       
            $collection = $this->_mediaappearancemediaappearanceFactory->create()
                    ->addStoreFilter($this->_storeManager->getStore()->getId())
                    ->addFieldToFilter('status', 1)
                    ->addOrder('gorder', 'asc');



        
        return $collection;
    }
    public function addCaption($caption,$type)
    {
        $html='';

        $html.='<div class="caption-block">';
        $html.='<div class="text-wrapper">';
        //
        if ($this->_helper->enableIconEnable()) {
            $iconClass=$this->_helper->enableIconClass();
          if($type=="image")
          {
            $html.='<h4 class="title" ><i class="'.$iconClass.'" "></i></h4>';
          }else{
            $html.='<h4 class="title" ><i class="fa fa-play-circle" style="font-size:50px"></i></h4>';
         
          }
            $html.='<h5 class="subtitle">'.$caption.'</h5>';
        } else {
            $html.='<h4 class="title"></h4>';
            $html.='<h5 class="subtitle">'.$caption.'</h5>';
        }

        
        ///$html.='<h4 class="title"><i class="budicon-play"></i></h4>';
        
        //$html.='<h5 class="subtitle">Subtitle here</h4>';
        $html.='</div>';
        $html.='</div>';
        return $html;
    }
    public function getthumbsdata($image)
    {
        $html='';
            $html.='   <img class="item"  src="'.$image.'" data-src="'.$image.'"/>';
        
        
        return $html;
    }
    public function addzoomEffect()
    {
        $html='';
        if ($this->_helper->enableZoom()) {
           
            $html.=$this->_helper->zoomEffect().' '.$this->_helper->zoomSpeed();
        }
        return $html;
    }
    public function addFilter($mediagallery)
    {
        $html='';
      
            $gallery_labels = $mediagallery;
            $html.='<div class="ftg-filters">';

            $html.='<input id="current_gallery" type="hidden" name="filter" value="0">';
            $html.='<a id="alllll" gal-id="all" href="#ftg-set-ftgall">All</a>';
            foreach ($gallery_labels as $gallery_label) {
               // if ($gallery_label['show_in']=="1" ||$gallery_label['show_in']=="3") {
                    $html.='<a   gal-id="'.$gallery_label["mediagallery_id"].'"    href="#ftg-set-'.$gallery_label["mediagallery_id"].'">'.$gallery_label['gal_name'].'</a>';
               // }
            }

            $html.='</div>';
            //$html.=$this->_helper->getCaptionPosition().' '.$this->_helper->getCaptionAnimation().' '.$this->_helper->getCaptionAlingment().' '.$this->_helper->getCaptionColor();
      //  }
        return $html;
    }
    public function addsmeffect()
    {
        $html='';
        if ($this->_helper->enableSocialMedia()) {
           
            $html.=$this->_helper->getSocialMediaPosition().' '.$this->_helper->getSocialMediaStyle();
        }
        return $html;
    }
    public function addCaptiononGallery()
    {
        $html='';
        if ($this->_helper->enableCaption()) {
            $html.=$this->_helper->getCaptionPosition().' '.$this->_helper->getCaptionAnimation().' '.$this->_helper->getCaptionAlingment().' '.$this->_helper->getCaptionColor();
        }
        return $html;
    }
    
    public function mediagalleryHtml($gallery,$parameter)
    {
        $html='';
      
        $html.='<div id="page">';
        $html.='<div id="gallery" class="final-tiles-gallery  '.$this->addzoomEffect().' '.$this->addCaptiononGallery().' '.$this->addsmeffect().'">';
        
        $html.=$this->addFilter($gallery);

        $html.=' <div class="ftg-items">';
        
       // $media=$this->_mediaappearancemediaappearance->getRelatedMediaVideo($gallery->getData()['mediagallery_id']);

        foreach ($gallery as $_gal) {
            //getRelatedMediaOnDrop
            //$media=$this->_mediaappearancemediaappearance->getRelatedMediaVideo($_gal['mediagallery_id'],$parameter);
            $media=$this->_mediaappearancemediaappearance->getRelatedMediaOnDrop($_gal['mediagallery_id'],$parameter);
            
            foreach ($media as $image) {
            $html.=$this->_helper->createtiles($image);
            }
        }
        
        $html.=' </div>';
        $html.='</div>';
        $html.='</div>';

        return $html;
    }
    public function addfilterwithtiles($galleryid)
    {
        $html='';
       
            $html.='ftg-set-'.$galleryid;
        

        return $html;
    }
    /**
     * _prepareLayout
     * @return Layout
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
       
    }

    /**
     * getPagerHtml
     * @return html
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * getProduct
     * @param   $id
     * @return product model
     */
    public function getProduct($id)
    {
        $pro = $this->_product->load($id);
        return $pro;
    }

    /**
     * getCategory
     * @param  $id
     * @return category model
     */
    public function getCategory($id)
    {
        $model = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($id);

        return $model;
    }

    /**
     * getCMS
     * @param  $id
     * @return CMS page
     */
    public function getCMS($id)
    {
        $model = $this->_objectManager->create('Magento\CMS\Model\Page')->load($id);

        return $model;
    }

    /**
     * getTab
     * @return int
     */
    public function getTab()
    {
        $tabID = $this->getRequest()->getParam('tab');
        if ($tabID == 1 || $tabID == null) {
            $tab = "all-media";
        } elseif ($tabID == 2) {
            $tab = "product-media";
        } elseif ($tabID == 3) {
            $tab = "cat-media";
        } elseif ($tabID == 4) {
            $tab = "cms-media";
        }
        return $tab;
    }

    /**
     * getAjaxLoader
     * @return string
     */
    public function getAjaxLoader()
    {
        $loader = $this->_helper->getMediaUrl() . $this->_helper->getAjaxLoaderPath();
        return $loader;
    }
}
