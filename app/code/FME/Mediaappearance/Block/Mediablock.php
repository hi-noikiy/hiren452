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
 
class Mediablock extends \Magento\Framework\View\Element\Template
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


    protected $blockidentifier;
    protected $galid;
    protected $layoutType;
    protected $gallery_background_color;
    protected $tiles_col_width;
    protected $tile_enable_border;
    protected $tile_border_width;
    protected $tile_border_color;
    protected $tile_border_radius;
    protected $tile_enable_outline;
    protected $tile_outline_color;
    protected $tile_enable_shadow;
    protected $tile_shadow_blur;
    protected $tile_shadow_color;
    protected $tile_enable_overlay;
    protected $tile_overlay_opacity;
    protected $tile_overlay_color;
    protected $lightbox_type;
    protected $lightbox_slider_image_border_width;
    protected $lightbox_slider_image_border_color;
    protected $tiles_justified_row_height;
    protected $tiles_justified_space_between;
    protected $tiles_space_between_cols;
    protected $tiles_nested_optimal_tile_width;
    protected $tile_height;
    protected $tile_width;
    protected $theme_navigation_type;
    protected $theme_bullets_color;
    protected $carousel_autoplay;
    protected $carousel_autoplay_timeout;
    protected $carousel_autoplay_direction;


    //Filters

    protected $enablecaption;
    protected $captionposition;
    protected $captionanimation;
    protected $captionalignment;
    protected $captioncolor;
    protected $enableicons;
    protected $enablezoom;
    protected $zoomeffect;
    protected $zoomspeed;
    protected $enablesm;
    protected $smposition;
    protected $smstyle;
    protected $layout;



    protected $featured_videos;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \FME\Mediaappearance\Helper\Data $helper,
        \FME\Mediaappearance\Model\ResourceModel\Media\CollectionFactory $blockCollection,
        \FME\Mediaappearance\Model\Mediaappearance $mediaappearance,
        \FME\Mediaappearance\Model\Media $blockModel,
        \Magento\Framework\App\ResourceConnection $coreresource,
        array $data = []
    ) {
            $this->_blockCollection = $blockCollection;
            $this->_blockModel = $blockModel;
            $this->_mediaappearancemodel= $mediaappearance;
            $this->_coreresource = $coreresource;
            $this->_helper = $helper;
            parent::__construct($context, $data);
    }

   /**
    * _tohtml
    * @return html
    */
    protected function _tohtml()
    {
     
        $this->blockidentifier = $this->getBlockId();
        
        $this->galid = $this->getGalleryId();
        
        $this->layoutType = $this->getLayoutType();
        $this->gallery_background_color=$this->getGalleryBackgroundColor();
       $this->tiles_col_width=$this->getTilesColWidth();
       $this->tile_enable_border=$this->getTileEnableBorder();
       $this->tile_border_width=$this->getTileBorderWidth();
       $this->tile_border_color=$this->getTileBorderColor();
       $this->tile_border_radius=$this->getTileBorderRadius();
       $this->tile_enable_outline=$this->getTileEnableOutline();
       $this->tile_outline_color=$this->getTileOutlineColor();
       $this->tile_enable_shadow=$this->getTileEnableShadow();
       $this->tile_shadow_blur=$this->getTileShadowBlur();
       $this->tile_shadow_color=$this->getTileShadowColor();
       $this->tile_enable_overlay=$this->getTileEnableOverlay();
       $this->tile_overlay_opacity=$this->getTileOverlayOpacity();
       $this->tile_overlay_color=$this->getTileOverlayColor();
       $this->lightbox_type=$this->getLightboxType();
       $this->lightbox_slider_image_border_width=$this->getLightboxSliderImageBorderWidth();
        $this->lightbox_slider_image_border_color = $this->getLightboxSliderImageBorderColor();
        //yahan tak in UG
        
        $this->tiles_justified_row_height=$this->getTilesJustifiedRowHeight();
        $this->tiles_justified_space_between=$this->getTilesJustifiedSpaceBetween();
        $this->tiles_space_between_cols=$this->getTilesSpaceBetweenCols();
        $this->tiles_nested_optimal_tile_width=$this->getTilesNestedOptimalTileWidth();
        $this->tile_height=$this->getTileHeight();
        $this->tile_width=$this->getTileWidth();
        $this->theme_navigation_type=$this->getThemeNavigationType();
        $this->theme_bullets_color=$this->getThemeBulletsColor();
        $this->carousel_autoplay=$this->getCarouselAutoplay();
        $this->carousel_autoplay_timeout=$this->getCarouselAutoplayTimeout();
        $this->carousel_autoplay_direction=$this->getCarouselAutoplayDirection();
        //Filters



        $this->enablecaption= $this->getEnableCaption();
        $this->captionposition= $this->getCaptionPosition();
        $this->captionanimation= $this->getCaptionAnimation();
        $this->captionalignment= $this->getCaptionAlignment();
        $this->captioncolor= $this->getCaptionColor();
        $this->enableicons= $this->getEnableIcon();
        $this->enablezoom= $this->getEnableZoom();
        $this->zoomeffect= $this->getZoomEffect();
        $this->zoomspeed= $this->getZoomSpeed();
        $this->enablesm= $this->getEnableSm();
        $this->smposition= $this->getSmiconsPosition();
        $this->smstyle= $this->getSmiconsStyle();
        $this->layout= $this->getLayoutType1();
        
        
        $this->featured_videos=$this->getFeaturedVideos();
        
        $this->setTemplate("FME_Mediaappearance::block.phtml");
        return parent::_toHtml();
    }
    public function getBlock() 
    {
        return $this->blockidentifier;
    }

    //$this->layout

    public function enableThumbsInColuumn()
    {
        if($this->layout!=null)
        {
            return $this->layout;
        }
        return "final";
    }
    public function getTilesscarousel_autoplay_direction()
    {
        if($this->carousel_autoplay_direction!=null)
        {
            return $this->carousel_autoplay_direction;
        }
        return "right";
    }
    public function getTilesscarousel_autoplay_timeout()
    {
        if($this->carousel_autoplay_timeout!=null)
        {
            return $this->carousel_autoplay_timeout;
        }
        return "1000";
    }
    public function getTilesscarousel_autoplay()
    {
        if($this->carousel_autoplay!=null)
        {
            return $this->carousel_autoplay;
        }
        return "0";
    }
    public function getTilesstheme_bullets_color()
    {
        if($this->theme_bullets_color!=null)
        {
            return $this->theme_bullets_color;
        }
        return "#ffffff";
    }
    public function getTilesstheme_navigation_type()
    {
        if($this->theme_navigation_type!=null)
        {
            return $this->theme_navigation_type;
        }
        return "180";
    }
    public function getTilesstile_width()
    {
        if($this->tile_width!=null)
        {
            return $this->tile_width;
        }
        return "180";
    }
    public function getTilessstile_height()
    {
        if($this->tile_height!=null)
        {
            return $this->tile_height;
        }
        return "150";
    }
    public function getTilessstiles_nested_optimal_tile_width()
    {
        if($this->tiles_nested_optimal_tile_width!=null)
        {
            return $this->tiles_nested_optimal_tile_width;
        }
        return "150";
    }
    public function getTilessstiles_space_between_cols()
    {
        if($this->tiles_space_between_cols!=null)
        {
            return $this->tiles_space_between_cols;
        }
        return "10";
    }
    public function getTilessstiles_justified_space_between()
    {
        if($this->tiles_justified_space_between!=null)
        {
            return $this->tiles_justified_space_between;
        }
        return "10";
    }
    public function getTilessstiles_justified_row_height()
    {
        if($this->tiles_justified_row_height!=null)
        {
            return $this->tiles_justified_row_height;
        }
        return "250";
    }
    public function getTilessslightbox_slider_image_border_color()
    {
        if($this->lightbox_slider_image_border_color!=null)
        {
            return $this->lightbox_slider_image_border_color;
        }
        return "#ffffff";
    }
    public function getTilessslightbox_slider_image_border_width()
    {
        if($this->lightbox_slider_image_border_width!=null)
        {
            return $this->lightbox_slider_image_border_width;
        }
        return "5";
    }
    public function getTilessslightbox_type()
    {
        if($this->lightbox_type!=null)
        {
            return $this->lightbox_type;
        }
        return "compact";
    }
    public function getTilessstile_overlay_color()
    {
        if($this->tile_overlay_color!=null)
        {
            return $this->tile_overlay_color;
        }
        return "ffffff";
    }
    public function getTilessstile_overlay_opacity()
    {
        if($this->tile_overlay_opacity!=null)
        {
            return $this->tile_overlay_opacity;
        }
        return "0.4";
    }
    public function getTilessstile_enable_overlay()
    {
        if($this->tile_enable_overlay!=null)
        {
            return $this->tile_enable_overlay;
        }
        return "ffffff";
    }
    public function getTilessstile_shadow_color()
    {
        if($this->tile_shadow_color!=null)
        {
            return $this->tile_shadow_color;
        }
        return "ffffff";
    }
    public function getTilessstile_shadow_blur()
    {
        if($this->tile_shadow_blur!=null)
        {
            return $this->tile_shadow_blur;
        }
        return "20";
    }
    public function getTilessstile_enable_shadow()
    {
        if($this->tile_enable_shadow!=null)
        {
            return $this->tile_enable_shadow;
        }
        return "ffffff";
    }
    public function getTilesssOutlineColor()
    {
        if($this->tile_outline_color!=null)
        {
            return $this->tile_outline_color;
        }
        return "ffffff";
    }
    public function getTilesssEnableOutline()
    {
        if($this->tile_enable_outline!=null)
        {
            return $this->tile_enable_outline;
        }
        return "0";
    }
    public function getTilesssBorderRadius()
    {
        if($this->tile_border_radius!=null)
        {
            return $this->tile_border_radius;
        }
        return "20";
    }
    public function getTilesssBorderColor()
    {
        if($this->tile_border_color!=null)
        {
            return $this->tile_border_color;
        }
        return "000000";
    }
    public function getTilesssBorderWidth()
    {
        if($this->tile_border_width!=null)
        {
            return $this->tile_border_width;
        }
        return 0;
    }
    public function getTilesssEnableBorder()
    {
        if($this->tile_enable_border!=null)
        {
            return $this->tile_enable_border;
        }
        return 0;
    }
    public function getTilesGalleryBackgroundColor()
    {
        if($this->gallery_background_color!=null)
        {
            return (string)$this->gallery_background_color;
        }
        return "ffffff";
    }
    public function getTilesgetTilesColWidth()
    {
        if($this->tiles_col_width!=null)
        {
            return (string)$this->tiles_col_width;
        }
        return "250";
    }
    public function getLType()
    {
        return $this->layoutType;
    }
    /*public function getTileType()
    {
        return $this->tiletype;
    }*/

    //$this->featured_videos
    public function isFeaturedVideoEnable()
    {
    
        if ($this->featured_videos==1 ||$this->featured_videos=="1") {
            //print_r( $this->_helper->zoomSpeed());
            //exit;
            
            return true;
        }
        return false;
    }
    public function addCaptiononGallery()
    {
        $html='';
        if ($this->enablecaption==1 ||$this->enablecaption=="1") {
            $html.=$this->captionposition.' '.$this->captionanimation.' '.$this->captionalignment.' '.$this->captioncolor;
        }
        return $html;
    }
    public function addzoomEffect()
    {
        $html='';
        if ($this->enablezoom==1 ||$this->enablezoom=="1") {
            //print_r( $this->_helper->zoomSpeed());
            //exit;
            
            $html.=$this->zoomeffect.' '.$this->zoomspeed;
        }
        return $html;
    }
    public function addsmeffect()
    {
        $html='';
        if ($this->enablesm==1 ||$this->enablesm=="1") {
            //print_r( $this->_helper->zoomSpeed());
            //exit;
            
            $html.=$this->smposition.' '.$this->smstyle;
        }
        return $html;
    }
    public function getMediaHeadings($id)
    {
        $collection=[];
        //echo $this->galid;exit;
        if ($this->galid!=null) {

            //$collection=$this->getBlockMedia($this->galid);
            $collection = $this->_mediaappearancemodel->getCollection();
        $store=$this->_storeManager->getStore()->getId();
        $collection->getSelect()
        /*->join(
            ['media_gal' => $this->_coreresource->getTableName(
                'mediagallery'
            )
            ],
            'main_table.mediagallery_id = media_gal.mediagallery_id',
            ['*']
        )*/->join(
            ['store_table' => $this->_coreresource->getTableName(
                'fme_media_store'
            )
            ],
            'main_table.mediagallery_id = store_table.mediagallery_id',
            []
        )->where(' main_table.status=1')
        ->where(' store_table.store_id in (?)', [0, $store])
        ->where('main_table.mediagallery_id in ('.$id.')');
        
        //$collection->getSelect()->where('main_table.status = 1');
       // $collection->getSelect()->where('pht_item.status = 1');
       // $collection->getSelect()->order('main_table.img_order ASC');
        //$collection->getSelect()->where('main_table.mediagallery_id in ('.$id.')');
        //$collection->getSelect()->group('media_gal.gal_name');
      //  echo $collection->getSelect();
       // exit;
        }
        return $collection->getData();
        
    }
    public function addFilter($ids)
    {
        $html='';
       
            $gallery_labels = $this->getMediaHeadings($ids);
            $html.='<div class="ftg-filters">';

            $html.='<input id="current_gallery" type="hidden" name="filter" value="0">';
            $html.='<a id="alllll" gal-id="all" href="#ftg-set-ftgall">All</a>';
        foreach ($gallery_labels as $gallery_label) {
                 $html.='<a gal-id="'.$gallery_label["mediagallery_id"].'"  href="#ftg-set-'.$gallery_label["mediagallery_id"].'">'.$gallery_label['gal_name'].'</a>';
            
        }

            $html.='</div>';
            //$html.=$this->_helper->getCaptionPosition().' '.$this->_helper->getCaptionAnimation().' '.$this->_helper->getCaptionAlingment().' '.$this->_helper->getCaptionColor();

        
        return $html;
    }
    public function createtiles($_gimage)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $block = $objectManager->create('FME\Mediaappearance\Block\Mediaappearance');
                $html='';
            
            $image_path = $this->_helper->getthumblinkdata($_gimage["filethumb"]);
            $description = $_gimage["media_title"];
            
            $html.='  <div class="tile '.$block->addfilterwithtiles($_gimage["mediagallery_id"]).'  " >';
           
            //$html.='  <div class="tile" >';
            
            if($_gimage['mediatype']==3||$_gimage['mediatype']=="3")
            {
               
            $html.='  <a class="tile-inner" href="'.$image_path.'" >';
            }
            else{
                if($_gimage['mediatype']==1||$_gimage['mediatype']=="1"){
                $html.='  <a class="pop-video" href="'.$_gimage['videourl'].'" >';
                }
                elseif($_gimage['mediatype']==2||$_gimage['mediatype']=="2"){
                   
                  // exit; 
                  /*
                    <a href="#test-popup1" class="open-popup-link">
                    */
                    $html.='<a href="#test-popup'.$_gimage['mediaappearance_id'].'" class="open-popup-link">';
                }
            }
            //  }

            $html.=$block->getthumbsdata($image_path);
           
        if ($this->enablecaption==1||$this->enablecaption=="1") {
            if($_gimage['mediatype']==3||$_gimage['mediatype']=="3")
            {
            $html.=$block->addCaption($_gimage['media_title'],"image");
            } else {
                $html.=$block->addCaption($_gimage['media_title'],"video");
            }
        }

            $html.='  </a>';

            if($_gimage['mediatype']==2||$_gimage['mediatype']=="2")
            {
                $html.='<div id="test-popup'.$_gimage['mediaappearance_id'].'" class="white-popup mfp-hide">';
                $html.='<video id="player1" width="750" height="421" controls preload="none">';
                $html.='<source src="'.$this->_helper->getMediaUrl().'mediaappearance/files/'.$_gimage['filename'].'" type="video/mp4">';
                $html.=' </video>';
                $html.='</div>';
            }
            $html.=$this->addScoialMediaIcon($image_path);
            $html.='  </div>';

          



            return $html;
    }
    public function addScoialMediaIcon($image_path)
    {
        $html="";
        $html.=' <div class="ftg-social">';
        $html.=' <a href="'.$image_path.'" data-social="twitter"><i class="fa fa-twitter"></i></a>';
        $html.=' <a href="'.$image_path.'" data-social="facebook"><i class="fa fa-facebook"></i></a>';
        $html.=' <a href="'.$image_path.'" data-social="google-plus"><i class="fa fa-google"></i></a>';
        $html.='  <a href="'.$image_path.'" data-social="pinterest"><i class="fa fa-pinterest"></i></a>';
        $html.=' </div>';
        return $html;
    }
    
    public function generateMediagalleryFilter()
    {
        //echo $this->getBlock();
       // exit;

        //echo $this->lengthofblockid($this->galleryidentifier);
       // exit;
        //$this->galleryidentifier
       // if ($this->galleryidentifier!=null) {
            //$collection=$this->getPhotoGalleryImagesbyId($this->galleryidentifier);
           //print_r($this->gallerytype);
           //exit;
           $collection=$this->getMediaForBlock();
                $html='';
                $html.='<div id="page'.$this->getBlock().'">';
                $html.='<div id="gallery'.$this->getBlock().'" class="final-tiles-gallery  '.$this->addzoomEffect().' '.$this->addCaptiononGallery().' '.$this->addsmeffect().'">';
                
             if (count($collection)>0) {
              
                 //$myArray = explode(',', $this->galleryidentifier);
                    $html.=$this->addFilter($this->galid);
                


                $html.=' <div class="ftg-items">';
                $media=$collection;
                foreach ($media as $image) {
                    $html.=$this->createtiles($image);
                    }
                $html.=' </div>';


                    }
                $html.='</div>';
                $html.='</div>';
                return $html;
            
        //}
        return $html;
    }
    public function getBlockMedia($id)
    {


        $collection = $this->_blockModel->getCollection();
        $store=$this->_storeManager->getStore()->getId();
        $collection->getSelect()->join(
            ['media_table' => $this->_coreresource->getTableName(
                'fme_mediagallery'
            )
            ],
            'main_table.mediagallery_id = media_table.mediagallery_id',
            ['*']
        )->join(
            ['store_table' => $this->_coreresource->getTableName(
                'fme_media_store'
            )
            ],
            'main_table.mediagallery_id = store_table.mediagallery_id',
            []
        )->where(' store_table.store_id in (?)', [0, $store]);
        //->where('main_table.show_in in (?)', [0,2]);
        
        $collection->getSelect()->where('main_table.status = 1 and media_table.status=1 ');
       // $collection->getSelect()->where('pht_item.status = 1');
       // $collection->getSelect()->order('main_table.img_order ASC');
            if($this->isFeaturedVideoEnable())
            {
                $collection->getSelect()->where('main_table.mediatype in(1,2)');   
                $collection->getSelect()->where('main_table.featured_media = 1');   
            }


        $collection->getSelect()->where('main_table.mediagallery_id in ('.$id.')');
        
        //echo $collection->getSelect();
        //exit;
       // return $collection;
       // print_r($collection->getData());
       // exit;
        /*$collection = $this->_mediaappearancemodel->getCollection();
        
        $collection->getSelect()->join(
            ['pht_item'=> $this->_coreresource->getTableName('mediaappearance')],
            'main_table.mediagallery_id = pht_item.mediagallery_id'
        )->join(
            ['store_table' => $this->_coreresource->getTableName(
                'fme_media_store'
            )
            ],
            'main_table.mediagallery_id = store_table.mediagallery_id',
            []
        )->where(' store_table.store_id in (?)', [0, 1]);
        //->where('main_table.show_in in (?)', [0,2]);
        
        $collection->getSelect()->where('main_table.status = 1');
        $collection->getSelect()->where('pht_item.status = 1');
       // $collection->getSelect()->order('main_table.img_order ASC');
        $collection->getSelect()->where('main_table.mediagallery_id in (13,10)');
        
        //echo $collection->getSelect();
        //exit;
     return $collection;*/
        
        return $collection;
    }

    public function getMediaForBlock()
    {
        $collection=[];
        //echo $this->galid;exit;
        if ($this->galid!=null) {
            $collection=$this->getBlockMedia($this->galid);
  
        }
      //  print_r($collection->getData());exit;
        return $collection->getData();
    }
    public function getBlockVideos()
    {
        $block = $this->_blockModel->load($this->blockidentifier);
        $block_id = $block->getMediaBlockId();

        $collection = $this->_blockModel->getRelatedMedia($block_id);

        return $collection;
    }
    
    /**
     * getMediaBlock
     * @return block model
     */
    public function getMediaBlock()
    {
        $block = $this->_blockModel->load($this->blockidentifier);
        return $block;
    }
}