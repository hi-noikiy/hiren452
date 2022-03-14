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
namespace FME\Mediaappearance\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\Store;
use Magento\Store\Model\ScopeInterface;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_MEDIALIST_GALLERYTYPE= 'mediaappearance/medialist/gallerytype';
    const XML_PATH_YOUTUBE_API_KEY = 'catalog/product_video/youtube_api_key';
    const XML_PATH_MODULE_ENABLE = 'mediaappearance/general/enable_module';
    const XML_PATH_PLAY_VIDEO = 'mediaappearance/general/play_video';
    const XML_PATH_MEDIALIST_PAGETITLE = 'mediaappearance/medialist/page_title';
    const XML_PATH_MEDIALIST_METAKEY = 'mediaappearance/medialist/meta_keywords';
    const XML_PATH_MEDIALIST_METADESP = 'mediaappearance/medialist/meta_desp';
    const XML_PATH_MEDIALIST_PAGINATION = 'mediaappearance/medialist/media_per_page';
    const XML_PATH_VIDEOPOPUP_WIDTH = 'mediaappearance/videopopup/popup_width';
    const XML_PATH_VIDEOPOPUP_HEIGHT = 'mediaappearance/videopopup/popup_height';
    const XML_PATH_VIDEOPOPUP_AUTOPLAY = 'mediaappearance/videopopup/autoplay';
   
    const XML_PATH_IMAGE_WIDTH = 'mediaappearance/mediaimage/thumb_width';
    const XML_PATH_IMAGE_HEIGHT = 'mediaappearance/mediaimage/thumb_height';
    const XML_PATH_IMAGE_COLOR = 'mediaappearance/mediaimage/bg_color';
    const XML_PATH_IMAGE_ASPECT_RATIO = 'mediaappearance/mediaimage/aspect_ration';
    const XML_PATH_IMAGE_FRAME = 'mediaappearance/mediaimage/frame_thumb';
    
    const XML_PATH_SEO_IDENTIFIER = 'mediaappearance/seo/seo_url_identifier';
    const XML_PATH_SEO_SUFFIX = 'mediaappearance/seo/seo_url_suffix';
    const XML_PATH_AJAX_LOADER = 'mediaappearance/ajaxloader/placeholder';
    const XML_PATH_FEATUREDVIDEO_TITLE = 'mediaappearance/featuredvideo/title';
    const XML_PATH_ENABLE_PRODUCT_TAB = 'mediaappearance/medialist/enable_productvideos';
    const XML_PATH_ENABLE_CAT_TAB = 'mediaappearance/medialist/enable_catvideos';
    //const XML_PATH_ENABLE_CMS_TAB = 'mediaappearance/medialist/enable_cmsvideos';


    const XML_PATH_ENABLE_IN_COLUMN      = 'mediaappearance/mediagallerytilesettings/enable_Column';
   
    //New Media gallery 

    const XML_PATH_CAPTION_ENABLE          = 'mediaappearance/mediagallerytilesettings/caption';
    const XML_PATH_CAPTION_POSITION         = 'mediaappearance/mediagallerytilesettings/caption_position';
    const XML_PATH_CAPTION_ALINGNMENT          = 'mediaappearance/mediagallerytilesettings/caption_align';
    const XML_PATH_CAPTION_ANIMATION          = 'mediaappearance/mediagallerytilesettings/caption_animation';
    const XML_PATH_CAPTION_COLOR         = 'mediaappearance/mediagallerytilesettings/caption_colorscheme';
    const XML_PATH_CAPTION_ICON_ENABLE        = 'mediaappearance/mediagallerytilesettings/icons';
    const XML_PATH_CAPTION_ICON_NAME        = 'mediaappearance/mediagallerytilesettings/icons_list';
    const XML_PATH_SM_ENABLE       = 'mediaappearance/mediagallerytilesettings/social_media';
    const XML_PATH_SM_POSITION       = 'mediaappearance/mediagallerytilesettings/social_media_icon_pos';
    const XML_PATH_SM_STYLE     = 'mediaappearance/mediagallerytilesettings/social_media_icon_style';
    const XML_PATH_ENABLE_ENLARGEMENT     = 'mediaappearance/mediagallerytilesettings/allow_enlargment';
    const XML_PATH_MIN_TILE_WIDTH    = 'mediaappearance/mediagallerytilesettings/mintilewidth';
    const XML_PATH_ZOOM_ENABLE        = 'mediaappearance/mediagallerytilesettings/zoom';
    const XML_PATH_ZOOM_EFFECT        = 'mediaappearance/mediagallerytilesettings/zoom_effect';
    const XML_PATH_ZOOM_SPEED       = 'mediaappearance/mediagallerytilesettings/zoom_speed';
    const XML_PATH_MARGIN_ENABLE      = 'mediaappearance/mediagallerytilesettings/margin';
    const XML_PATH_MARGIN_SIZE      = 'mediaappearance/mediagallerytilesettings/margin_list';
    



    const XML_PATH_MAG_OPTION      = 'mediaappearance/photogallerypopusetting/popuooptions';
    const XML_PATH_POPUP_GAL_ENABLE      = 'mediaappearance/photogallerypopusetting/enablepopupgallery';
    const XML_PATH_POPUP_TIME     = 'mediaappearance/photogallerypopusetting/popuptime';
    const XML_PATH_POPUP_NAV_CLICK     = 'mediaappearance/photogallerypopusetting/enablepopupgalleryclick';
    
    
    //New Media gallery  End
    /**
     * @param \Magento\Framework\App\Helper\Context              $context
     * @param \Magento\Framework\ObjectManagerInterface          $objectManager
     * @param \Magento\Framework\Registry                        $registry
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager


     * @param \FME\Mediaappearance\Model\MediaappearanceFactory  $mediaappearancemediaappearanceFactory
     * @param \FME\Mediaappearance\Model\Mediaappearance         $mediaappearancemediaappearance
     * @param \Magento\Framework\Image\Factory                   $imageFactory
     * @param \Magento\Framework\App\Resource                    $coreResource
     */





     //pRODUCT Media
     const XML_PATH_THUMBS_WIDTH = 'mediaappearance/thumbsetting/thumb_width';
     const XML_PATH_THUMBS_HEIGHT = 'mediaappearance/thumbsetting/thumb_height';
     const XML_PATH_THUMBS_BORDER_EFFECT = 'mediaappearance/thumbsetting/thumb_border_effect';
     const XML_PATH_THUMBS_BORDER_WIDTH = 'mediaappearance/thumbsetting/thumb_border_width';
     const XML_PATH_THUMBS_BORDER_COLOR = 'mediaappearance/thumbsetting/thumb_border_color';
     const XML_PATH_THUMBS_OVER_BORDER_WIDTH = 'mediaappearance/thumbsetting/thumb_over_border_width';
     const XML_PATH_THUMBS_OVER_BORDER_COLOR = 'mediaappearance/thumbsetting/thumb_over_border_color';
     const XML_PATH_THUMBS_SELECTED_BORDER_WIDTH = 'mediaappearance/thumbsetting/thumb_selected_border_width';
     const XML_PATH_THUMBS_SELECTED_BORDER_COLOR = 'mediaappearance/thumbsetting/thumb_selected_border_color';
     const XML_PATH_THUMBS_ROUND_CORNER_RADIUS = 'mediaappearance/thumbsetting/thumb_round_corners_radius';
     const XML_PATH_THUMBS_COLOR_OVERLAY_EFFECT = 'mediaappearance/thumbsetting/thumb_color_overlay_effect';
     const XML_PATH_THUMBS_OVERLAY_COLOR = 'mediaappearance/thumbsetting/thumb_overlay_color';
     const XML_PATH_THUMBS_OVERLAY_OPACITY = 'mediaappearance/thumbsetting/thumb_overlay_opacity';
     const XML_PATH_THUMBS_OVERLAY_REVERSE = 'mediaappearance/thumbsetting/thumb_overlay_reverse';
     const XML_PATH_THUMBS_IMAGE_OVERLAY_EFFECT = 'mediaappearance/thumbsetting/thumb_image_overlay_effect';
     const XML_PATH_THUMBS_IMAGE_OVERLAY_TYPE = 'mediaappearance/thumbsetting/thumb_image_overlay_type';
     const XML_PATH_THUMBS_TRANSITION_DURATION = 'mediaappearance/thumbsetting/thumb_transition_duration';
     const XML_PATH_THUMBS_SHOW_LOADER = 'mediaappearance/thumbsetting/thumb_show_loader';
     const XML_PATH_THUMBS_LOADER_TYPE = 'mediaappearance/thumbsetting/thumb_loader_type';
     
     //Strip Setting
     const XML_PATH_STRIP_PAD_TOP = 'mediaappearance/stripepaneloptions/strippanel_padding_top';
     const XML_PATH_STRIP_PAD_BOTTOM = 'mediaappearance/stripepaneloptions/strippanel_padding_bottom';
     const XML_PATH_STRIP_PAD_LEFT = 'mediaappearance/stripepaneloptions/strippanel_padding_left';
     const XML_PATH_STRIP_PAD_RIGHT = 'mediaappearance/stripepaneloptions/strippanel_padding_right';
     const XML_PATH_STRIP_ENABLE_BUTTON = 'mediaappearance/stripepaneloptions/strippanel_enable_buttons';
     const XML_PATH_STRIP_PAD_BUTTON = 'mediaappearance/stripepaneloptions/strippanel_padding_buttons';
     const XML_PATH_STRIP_BUTTON_ROLE = 'mediaappearance/stripepaneloptions/strippanel_buttons_role';
     const XML_PATH_STRIP_ENABLE_HANDLE = 'mediaappearance/stripepaneloptions/strippanel_enable_handle';
     const XML_PATH_STRIP_HANDLE_ALIGN = 'mediaappearance/stripepaneloptions/strippanel_handle_align';
     const XML_PATH_STRIP_HANDLE_OFFSET = 'mediaappearance/stripepaneloptions/strippanel_handle_offset';
     const XML_PATH_STRIP_BACKGROUND_COLOR = 'mediaappearance/stripepaneloptions/strippanel_background_color';
     const XML_PATH_STRIP_THUMB_ALIGN = 'mediaappearance/stripepaneloptions/strip_thumbs_align';
     const XML_PATH_STRIP_SPACE_BT_THUMB = 'mediaappearance/stripepaneloptions/strip_space_between_thumbs';
     const XML_PATH_STRIP_THUMB_T_SENSITIVITY = 'mediaappearance/stripepaneloptions/strip_thumb_touch_sensetivity';
     const XML_PATH_STRIP_SCROLL_TO_THUMB_DUR = 'mediaappearance/stripepaneloptions/strip_scroll_to_thumb_duration';
     const XML_PATH_STRIP_SCROLL_THUMBS_AVIA = 'mediaappearance/stripepaneloptions/strip_control_avia';
     const XML_PATH_STRIP_CONTROL_TOUCH = 'mediaappearance/stripepaneloptions/strip_control_touch';
     //End Strip Setting
 
     //Grid Settings
     const XML_PATH_GRID_VER_SCROLL = 'mediaappearance/gridpaneloptions/gridpanel_vertical_scroll';
     const XML_PATH_GRID_GRID_ALIGN = 'mediaappearance/gridpaneloptions/gridpanel_grid_align';
     const XML_PATH_GRID_PAD_TOP = 'mediaappearance/gridpaneloptions/gridpanel_padding_border_top';
     const XML_PATH_GRID_PAD_BOTTOM = 'mediaappearance/gridpaneloptions/gridpanel_padding_border_bottom';
     const XML_PATH_GRID_PAD_LEFT = 'mediaappearance/gridpaneloptions/gridpanel_padding_border_left';
     const XML_PATH_GRID_PAD_RIGHT = 'mediaappearance/gridpaneloptions/gridpanel_padding_border_right';
     
     const XML_PATH_GRID_ARROWS_A_V = 'mediaappearance/gridpaneloptions/gridpanel_arrows_align_vert';
     const XML_PATH_GRID_ARROWS_P_V = 'mediaappearance/gridpaneloptions/gridpanel_arrows_padding_vert';
     const XML_PATH_GRID_ARROWS_A_H = 'mediaappearance/gridpaneloptions/gridpanel_arrows_align_hor';
     const XML_PATH_GRID_ARROWS_P_H = 'mediaappearance/gridpaneloptions/gridpanel_arrows_padding_hor';
     
     const XML_PATH_GRID_SPACE_BT_ARROWS = 'mediaappearance/gridpaneloptions/gridpanel_space_between_arrows';
     const XML_PATH_GRID_ARROWS_ON = 'mediaappearance/gridpaneloptions/gridpanel_arrows_always_on';
     const XML_PATH_ENABLE_HANDLE = 'mediaappearance/gridpaneloptions/gridpanel_enable_handle';
     const XML_PATH_HANDLE_ALIGN = 'mediaappearance/gridpaneloptions/gridpanel_handle_align';
     const XML_PATH_HANDLE_OFFSET = 'mediaappearance/gridpaneloptions/gridpanel_handle_offset';
     const XML_PATH_HANDLE_BACK_COLOR = 'mediaappearance/gridpaneloptions/gridpanel_background_color';
     const XML_PATH_HANDLE_PANES_DIR = 'mediaappearance/gridpaneloptions/grid_panes_direction';
     
     const XML_PATH_GRID_NO_COLUMNS = 'mediaappearance/gridpaneloptions/grid_num_cols';
     const XML_PATH_GRID_SPACE_BT_COLUMNS = 'mediaappearance/gridpaneloptions/grid_space_between_cols';
     const XML_PATH_GRID_SPACE_BT_ROWS = 'mediaappearance/gridpaneloptions/grid_space_between_rows';
     const XML_PATH_GRID_TRANSITION_DUR = 'mediaappearance/gridpaneloptions/grid_transition_duration';
     const XML_PATH_GRID_CAROUSAL = 'mediaappearance/gridpaneloptions/grid_carousel';
     
     //Cat media View 
     const XML_PATH_CMSMV_ENABLE = 'mediaappearance/cmsview/enablecms';
     
     const XML_PATH_CMSMV_TYPE = 'mediaappearance/cmsview/mediaview';
     const XML_PATH_CMSMV_VIEW = 'mediaappearance/cmsview/view';

     //End of Cat Media View
    //
      //Cat media View 
      const XML_PATH_CMV_ENABLE = 'mediaappearance/categoryview/enablecat';
      
      const XML_PATH_CMV_TYPE = 'mediaappearance/categoryview/mediaview';
      const XML_PATH_CMV_VIEW = 'mediaappearance/categoryview/view';
 
      //End of Cat Media View
     // 
     //product media View 
     //const XML_PATH_PMV_TYPE = 'mediaappearance/compactview/mediaview';
    
     const XML_PATH_PMV_ENABLE = 'mediaappearance/compactview/enablepro';
    
     const XML_PATH_PMV_TYPE = 'mediaappearance/compactview/mediaview';
     const XML_PATH_PMV_VIEW = 'mediaappearance/compactview/view';

     //End of Product Media View
     //grid_num_cols
     //gridpanel_space_between_arrows
     //
     //End Grid Settings
 
     //Start Slider
     const XML_PATH_SLIDER_TRANS = 'mediaappearance/slideoptions/slider_transition';
     const XML_PATH_SLIDER_TRANS_DUR = 'mediaappearance/slideoptions/slider_transition_speed';
     
     const XML_PATH_SLIDER_CONTROL_SWIPE = 'mediaappearance/slideoptions/slider_control_swipe';
     const XML_PATH_SLIDER_CONTROL_ZOOM = 'mediaappearance/slideoptions/slider_control_zoom';
     
     const XML_PATH_SLIDER_LOADER_TYPE = 'mediaappearance/slideoptions/slider_loader_type';
     const XML_PATH_SLIDER_LOADER_COLOR = 'mediaappearance/slideoptions/slider_loader_color';
    
     const XML_PATH_SLIDER_ENABLE_BULLET = 'mediaappearance/slideoptions/slider_enable_bullets1';
     const XML_PATH_SLIDER_BULLET_HOR = 'mediaappearance/slideoptions/slider_bullets_align_hor';
     const XML_PATH_SLIDER_BULLET_VER = 'mediaappearance/slideoptions/slider_bullets_align_vert';
     
     const XML_PATH_SLIDER_ENABLE_ARROWS = 'mediaappearance/slideoptions/slider_enable_arrows';
     const XML_PATH_SLIDER_ENABLE_P_INDICATOR = 'mediaappearance/slideoptions/slider_enable_progress_indicator';
     const XML_PATH_SLIDER_P_I_TYPE = 'mediaappearance/slideoptions/slider_progress_indicator_type';
     const XML_PATH_SLIDER_P_I_V_ALIGN = 'mediaappearance/slideoptions/slider_progress_indicator_align_vert';
     const XML_PATH_SLIDER_PB_COLOR = 'mediaappearance/slideoptions/slider_progressbar_color';
     const XML_PATH_SLIDER_PB_OPACITY = 'mediaappearance/slideoptions/slider_progressbar_opacity';
     const XML_PATH_SLIDER_PB_L_WIDTH = 'mediaappearance/slideoptions/slider_progressbar_line_width';
     
     const XML_PATH_SLIDER_PP_TYPE_FILL = 'mediaappearance/slideoptions/slider_progresspie_type_fill';
     const XML_PATH_SLIDER_PP_COLOR_1 = 'mediaappearance/slideoptions/slider_progresspie_color1';
     const XML_PATH_SLIDER_PP_COLOR_2 = 'mediaappearance/slideoptions/slider_progresspie_color2';
     const XML_PATH_SLIDER_PP_S_WIDTH = 'mediaappearance/slideoptions/slider_progresspie_stroke_width';
     const XML_PATH_SLIDER_PP_WIDTH = 'mediaappearance/slideoptions/slider_progresspie_width';
     const XML_PATH_SLIDER_PP_HEIGHT = 'mediaappearance/slideoptions/slider_progresspie_height';
     
     const XML_PATH_SLIDER_ENABLE_PB = 'mediaappearance/slideoptions/slider_enable_play_button';
     const XML_PATH_SLIDER_PB_H = 'mediaappearance/slideoptions/slider_play_button_align_hor';
     const XML_PATH_SLIDER_PB_V = 'mediaappearance/slideoptions/slider_play_button_align_vert';
     
     const XML_PATH_SLIDER_ENABLE_FS = 'mediaappearance/slideoptions/slider_enable_fullscreen_button';
     const XML_PATH_SLIDER_FS_H = 'mediaappearance/slideoptions/slider_fullscreen_button_align_hor';
     const XML_PATH_SLIDER_FS_V = 'mediaappearance/slideoptions/slider_fullscreen_button_align_vert';
     
     const XML_PATH_SLIDER_CONTROL_ALWAYS_ON= 'mediaappearance/slideoptions/slider_controls_always_on';
     const XML_PATH_SLIDER_CONTROL_APEAR_ONTAP= 'mediaappearance/slideoptions/slider_controls_appear_ontap';
     const XML_PATH_SLIDER_CONTROL_APEAR_DUR= 'mediaappearance/slideoptions/slider_controls_appear_duration';
     
     const XML_PATH_SLIDER_ENABLE_TEXT_PANEL= 'mediaappearance/slideoptions/slider_enable_text_panel';
     const XML_PATH_SLIDER_TEXT_PANEL_BGCOLOR= 'mediaappearance/slideoptions/slider_textpanel_bg_color';
     const XML_PATH_SLIDER_TEXT_PANEL_OPACITY= 'mediaappearance/slideoptions/slider_textpanel_bg_opacity';
     //End Slider
 



 
     //Media End
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \FME\Mediaappearance\Model\MediaappearanceFactory $mediaappearancemediaappearanceFactory,
        \FME\Mediaappearance\Model\Media $mediaappearancemediaappearance,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\App\ResourceConnection $coreResource
    ) {

        $this->_mediaappearancemediaappearanceFactory = $mediaappearancemediaappearanceFactory;
        $this->_mediaappearancemediaappearance = $mediaappearancemediaappearance;
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_eventManager = $context->getEventManager();
        $this->_imageFactory = $imageFactory;
        $this->_resource = $coreResource;

        parent::__construct($context);
    }
    /*public function videoType($url)
    {
        if (strpos($url, 'youtube') > 0) {
            return 'youtube';
        } elseif (strpos($url, 'vimeo') > 0) {
            return 'vimeo';
        } else {
            return 'unknown';
        }
    }*/
    //Magnific Pop Up

    
    public function enablePopupNavOnCLick()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_POPUP_NAV_CLICK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getPopupTime()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_POPUP_TIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function enablegalonPopUp()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_POPUP_GAL_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //End With Magnific popUp


    public function getGalleryType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MEDIALIST_GALLERYTYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableCaption()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getCaptionPosition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_POSITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCaptionAlingment()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_ALINGNMENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCaptionColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCaptionAnimation()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_ANIMATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function enableIconEnable()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_ICON_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function enableIconClass()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_ICON_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function enableSocialMedia()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SM_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getSocialMediaPosition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SM_POSITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getSocialMediaStyle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SM_STYLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function enableZoom()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ZOOM_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function zoomEffect()
    {
       // echo "Zoom";
        return $this->scopeConfig->getValue(
            self::XML_PATH_ZOOM_EFFECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function zoomSpeed()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ZOOM_SPEED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function enableMargin()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MARGIN_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getMarginSize()
    {
        $size=$this->scopeConfig->getValue(
            self::XML_PATH_MARGIN_SIZE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($size==null|| $size==0) {
            return "10";
        } else {
            return $size;
        }
        /*return $this->scopeConfig->getValue(
            self::XML_PATH_MARGIN_SIZE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );*/
    }
    public function enableThumbsInColuumn()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_IN_COLUMN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }




     //Category
     public function enableCMSMV()
    {
        //
        return $this->scopeConfig->getValue(
            self::XML_PATH_CMSMV_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

    }
     public function getNewTHemeCMS()
     {
         //
         return $this->scopeConfig->getValue(
             self::XML_PATH_CMSMV_VIEW,
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
 
     }
     public function getPMVTypeCMS()
     {
         //
         return $this->scopeConfig->getValue(
             self::XML_PATH_CMSMV_TYPE,
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
 
     }
     //End Category



    //Category
    public function enableCMV()
    {
        //
        return $this->scopeConfig->getValue(
            self::XML_PATH_CMV_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

    }
    public function getNewTHemeCat()
    {
        //
        return $this->scopeConfig->getValue(
            self::XML_PATH_CMV_VIEW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

    }
    public function getPMVTypeCat()
    {
        //
        return $this->scopeConfig->getValue(
            self::XML_PATH_CMV_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

    }
    //End Category


    



    public function enablePMV()
    {
        //
        return $this->scopeConfig->getValue(
            self::XML_PATH_PMV_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

    }
    public function getNewTHeme()
    {
        //
        return $this->scopeConfig->getValue(
            self::XML_PATH_PMV_VIEW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

    }
    public function getPMVType()
    {
        //
        return $this->scopeConfig->getValue(
            self::XML_PATH_PMV_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

    }
    //Start Slider
    
    public function getNewSliderTransition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_TRANS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewSliderTransitionDuration()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_TRANS_DUR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderControlSwipe()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_CONTROL_SWIPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderControlZoom()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_CONTROL_ZOOM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderLoaderType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_LOADER_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function videoType($url)
    {   
        if (strpos($url, 'youtube') !== FALSE) {
            return 'youtube';
        } elseif (strpos($url, 'vimeo')  !== FALSE) {
            return 'vimeo';
        } else {
            return 'unknown';
        }
    }
    //XML_PATH_SLIDER_LOADER_COLOR
    public function getNewSliderLoaderColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_LOADER_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderEnableBullet()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_BULLET,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderBulletHor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_BULLET_HOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderBulletVer()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_BULLET_VER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderEnableArrows()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_ARROWS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPIndicator()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_P_INDICATOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPIndicatorType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_P_I_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPIndicatorVAlign()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_P_I_V_ALIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_PB_COLOR
    public function getNewSliderPBarColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PB_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_PB_OPACITY
    public function getNewSliderPBarOpacity()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PB_OPACITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_PB_L_WIDTH
    public function getNewSliderPBLWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PB_L_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_PP_TYPE_FILL
    public function getNewSliderPPTYPEFill()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_TYPE_FILL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_PP_COLOR_1
    public function getNewSliderPPColor1()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_COLOR_1,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewSliderPPColor2()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_COLOR_2,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPPSWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_S_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPPWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPPHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_ENABLE_PB
    public function getNewSliderEnablePB()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_PB,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_PB_H
    public function getNewSliderEnablePBHor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PB_H,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewSliderEnablePBVer()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PB_V,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_ENABLE_FS
    public function getNewSliderEnableFS()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_FS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderEnableFSHor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_FS_H,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderEnableFSVer()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_FS_V,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_CONTROL_ALWAYS_ON
    public function getNewSliderControlAlwaysOn()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_CONTROL_ALWAYS_ON,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderControlAppearOntap()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_CONTROL_APEAR_ONTAP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_CONTROL_APEAR_DUR
    public function getNewSliderControlAppearDur()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_CONTROL_APEAR_DUR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_ENABLE_TEXT_PANEL
    public function getNewSliderEnableTextpanel()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_TEXT_PANEL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_TEXT_PANEL_BGCOLOR
    public function getNewSliderTextpanelBGColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_TEXT_PANEL_BGCOLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_SLIDER_TEXT_PANEL_OPACITY
    public function getNewSliderTextpanelOpacity()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_TEXT_PANEL_OPACITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //End Slider
    //start Grid
    public function getNewGridVScrol()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_VER_SCROLL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridGridAlign()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_GRID_ALIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridPadTop()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_PAD_TOP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_GRID_PAD_BOTTOM
    public function getNewGridPadBottom()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_PAD_BOTTOM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridPadLeft()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_PAD_LEFT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridPadRight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_PAD_RIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_GRID_ARROWS_A_V
    public function getNewGridArrowAlignVertical()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_ARROWS_A_V,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridArrowPaddingVertical()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_ARROWS_P_V,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridArrowAlignHozizontal()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_ARROWS_A_H,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
//
    public function getNewGridArrowPaddingHozizontal()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_ARROWS_P_H,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridArrowSpace()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_SPACE_BT_ARROWS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridArrowOn()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_ARROWS_ON,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //XML_PATH_ENABLE_HANDLE
    public function getNewGridEnableHandle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_HANDLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridHandleAlign()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HANDLE_ALIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridHandleOffset()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HANDLE_OFFSET,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridHandleBackColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HANDLE_BACK_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridHandlePanesDirection()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HANDLE_PANES_DIR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridNoColumns()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_NO_COLUMNS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridSpaceBtColumns()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_SPACE_BT_COLUMNS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridSpaceBtRows()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_SPACE_BT_ROWS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridTransitionDuration()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_TRANSITION_DUR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridCarousel()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_CAROUSAL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //End Grid
    //Strip Setting
    public function getNewStripPadtop()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_PAD_TOP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewStripPadBottom()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_PAD_BOTTOM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewStripPadLeft()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_PAD_LEFT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewStripPadRight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_PAD_RIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewStripEnableButon()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_ENABLE_BUTTON,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewStripPaddingButon()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_PAD_BUTTON,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
//
    public function getNewStripButonRole()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_BUTTON_ROLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
//
    public function getNewStripEnablehandle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_ENABLE_HANDLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewStriphandleAlign()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_HANDLE_ALIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getNewStriphandleOffset()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_HANDLE_OFFSET,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewStripBackgroundColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_BACKGROUND_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewStripThumbAlign()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_THUMB_ALIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getNewStripSpacebtthumbs()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_SPACE_BT_THUMB,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewStripThumbTouchSensitivity()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_THUMB_T_SENSITIVITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewStripScrollToThumbDur()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_SCROLL_TO_THUMB_DUR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewStripThumbAvia()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_SCROLL_THUMBS_AVIA,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewStripControlTouch()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_CONTROL_TOUCH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //End Strip Setting
    //Thumbs Setting
    public function getNewThumbsWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewThumbsHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
 
    public function getNewThumbsBE()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_BORDER_EFFECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getNewThumbsBW()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_BORDER_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewThumbsBC()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_BORDER_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewThumbsOverBW()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_OVER_BORDER_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewThumbsOverBC()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_OVER_BORDER_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewThumbsSelectedBW()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_SELECTED_BORDER_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewThumbsSelectedBC()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_SELECTED_BORDER_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getNewThumbsRoundCorner()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_ROUND_CORNER_RADIUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsColorOverlayEffect()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_COLOR_OVERLAY_EFFECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsOVerlayColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_OVERLAY_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsOVerlayOpacity()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_OVERLAY_OPACITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsOVerlayReverse()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_OVERLAY_REVERSE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsImageOverlayEffect()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_IMAGE_OVERLAY_EFFECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsImageOverlayType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_IMAGE_OVERLAY_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsTransitionDuration()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_TRANSITION_DURATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbShowLoader()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_SHOW_LOADER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbLoaderType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_LOADER_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //End Thumbs Setting










    /**
     * getModuleEnable
     * @return boolean
     */
    public function getModuleEnable()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MODULE_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getBgcolor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_IMAGE_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getKeepframe()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_IMAGE_FRAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAspectratioflag()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_IMAGE_ASPECT_RATIO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function isEnabledInFrontend()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_PATH_MODULE_ENABLE, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

    /**
     * getVideoMode
     * @return boolean
     */
    public function getVideoMode()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PLAY_VIDEO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getPageTitle
     * @return string
     */
    public function getPageTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MEDIALIST_PAGETITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getMetaKeywords
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MEDIALIST_METAKEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getMetaDesp
     * @return string
     */
    public function getMetaDesp()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MEDIALIST_METADESP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getNumberOfItems
     * @return int
     */
    public function getNumberOfItems()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MEDIALIST_PAGINATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getPopupWidth
     * @return int
     */
    public function getPopupWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_VIDEOPOPUP_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getPopupHeight
     * @return int
     */
    public function getPopupHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_VIDEOPOPUP_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getPopupAutoPlay
     * @return boolean
     */
    public function getPopupAutoPlay()
    {
        //return false;
        return $this->scopeConfig->getValue(
            self::XML_PATH_VIDEOPOPUP_AUTOPLAY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getImageWidth
     * @return int
     */
    public function getImageWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_IMAGE_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getImageHeight
     * @return int
     */
    public function getImageHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_IMAGE_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getUrlIdentifier
     * @return string
     */
    public function getUrlIdentifier()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_IDENTIFIER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getUrlSuffix
     * @return string
     */
    public function getUrlSuffix()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_SUFFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * enableCMSVideoTab
     * @return boolean
     */
    
    /**
     * getMediaappearanceUrl
     * @return string
     */
    public function getMediaappearanceUrl()
    {
        $url = $this->getUrlIdentifier() . $this->getUrlSuffix();
        return $this->_storeManager->getStore()->getUrl($url);
    }

    /**
     * getMediaappearancePath
     * @return string
     */
    public function getMediaappearancePath()
    {
        $url = $this->getUrlIdentifier() . $this->getUrlSuffix();
        return $url;
    }

    /**
     * getAjaxLoaderPath
     * @return string
     */
    public function getAjaxLoaderPath()
    {
        return 'mediaappearance/ajax/' . $this->scopeConfig->getValue(
            self::XML_PATH_AJAX_LOADER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getFeaturedVideoTitle
     * @return string
     */
    public function getFeaturedVideoTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FEATUREDVIDEO_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * video_info
     * @param   $url
     * @return   array
     */
    public function videoinfo($url)
    {
        
        // Handle Youtube
        if (strpos($url, "youtube.com") !== false || strpos($url, "youtu.be") !== false) {
            $data = $this->getYouTubeInfo($url);
        } // End Youtube
        // Handle Vimeo
        elseif (strpos($url, "vimeo.com") !== false) {
            $data = $this->getVimeoInfo($url);
        } // End Vimeo
        // Handle Dailymotion
        elseif (strpos($url, "dailymotion.com") === true) {
            $data['video_type'] = 'dailymotion';
            $data['video_id'] = $url . '?autoPlay=1';
            return $data;
        } //End Dailymotion
        // Set false if invalid URL
        else {
            $data = false;
        }

        return $data;
    }

    /**
     * getYouTubeInfo
     * @param  $url
     * @return array
     */
    public function getYouTubeInfo($url)
    {

       // $url = parse_url($url);
        //echo "<pre>";
        //print_r($url);
        // exit;
      //  $vid = parse_str($url['query'], $output);
      //  $video_id = $output['v'];
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
             $video_id = $match[1];
        }
        $data['video_type'] = 'youtube';
        $data['video_id'] = $video_id;
        return $data;
    }

    /**
     * getVimeoInfo
     * @param  $url
     * @return array
     */
    public function getVimeoInfo($url)
    {
        $video_id = explode('vimeo.com/', $url);
        $video_id = $video_id[1];
        $data['video_type'] = 'vimeo';
        $data['video_id'] = $video_id;
        return $data;
    }

    /**
     * is_image
     * @param  $path
     * @return string
     */
    public function isimage($path)
    {

        $a = getimagesize($path);

        $image_type = $a[2];

        if (in_array($image_type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP])) {
            return "fancybox";
        } else {
            return "jwVideo";
        }
    }

    /**
     * getMediaUrl
     * @return string
     */
    public function getMediaUrl()
    {

        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
    }

    /**
     *
     * @param  $imgUrl
     * @param  $x
     * @param  $y
     * @param  $imagePath
     * @return string
     */
    public function resizeImage($imgUrl, $x = null, $y = null, $imagePath = null)
    {
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
        $baseScmsMediaURL = $mediaDirectory->getAbsolutePath();
        $x = $this->getImageWidth();
        $y = $this->getImageHeight();
        if ($x == null && $y == null) {
            $x = 200;
            $y = 200;
        }

        $imgPath = $this->splitImageValue($imgUrl, "path");
        $imgName = $this->splitImageValue($imgUrl, "name");



        /**
         * Path with Directory Seperator
         */
        $imgPath = str_replace("/", '/', $imgPath);

        /**
         * Absolute full path of Image
         */
        $imgPathFull = $baseScmsMediaURL . $imgPath . '/' . $imgName;


        /**
         * If Y is not set set it to as X
         */
        $width = $x;
        $y ? $height = $y : $height = $x;

        /**
         * Resize folder is widthXheight
         */
        $resizeFolder = $width . "X" . $height;

        /**
         * Image resized path will then be
         */
        $imageResizedPath = $baseScmsMediaURL . $imgPath . '/' . $resizeFolder . '/' . $imgName;

        /**
         * First check in cache i.e image resized path
         * If not in cache then create image of the width=X and height = Y
         */
        $colorArray = [];
        $color = "255,255,255";
        $colorArray = explode(",", $color);

        //print_r($colorArray); exit();
        if (!file_exists($imageResizedPath) && file_exists($imgPathFull)) :
            $imageObj = $this->_imageFactory->create($imgPathFull);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(false);
            $imageObj->resize($width, $height);
            $imageObj->save($imageResizedPath);
        endif;

        /**
         * Else image is in cache replace the Image Path with / for http path.
         */
        $imgUrl = str_replace('/', "/", $imgPath);

        /**
         * Return full http path of the image
         */
        return $this->getMediaUrl() . $imgUrl . "/" . $resizeFolder . "/" . $imgName;
    }

    /**
     * getMediaData
     * @param  $_item
     * @return array
     */
    public function getthumblinkdata($thumb)
    {
        if (strpos($thumb, 'https://') !== false) {
            return $thumb;

        }
        return $this->getMediaUrl().'mediaappearance/files/'.$thumb;
    }
    public function getMediaData($_item)
    {
        $data = [];
        $popupWidth = $this->getPopupWidth();
        $popupHeight = $this->getPopupHeight();
        $data_type = '';
        $playAgain = true;
        if ($_item["mediatype"] == "1") {
            $data_type = 'data-type="swf"';
            $class = 'fancybox';//$this->isimage($this->getMediaUrl() . $_item["filename"]);
            $videoURL = $this->getMediaUrl() . $_item["filename"];
            $videoRel = 'mediagal';
            if ($_item["filethumb"] != "") {
                if (strpos($_item["filethumb"], 'https://') !== false) {
                    $imgURL =$_item["filethumb"];
                } else {
                    $imgURL = $this->getMediaUrl() . $_item["filethumb"];
                }
            } else {
                $imgURL = $this->getMediaUrl() . "mediaappearance/video_icon.jpg";
            }
        } elseif ($_item["mediatype"] == "2") {
            $data_type = 'data-type="iframe"';
            //For Thumb
            $videoURL = $_item["videourl"];
            $videoData = $this->videoinfo($_item["videourl"]);
            if ($videoData !== false) {
                if ($_item["filethumb"] != "") {
                    if (strpos($_item["filethumb"], 'https://') !== false) {
                        $imgURL =$_item["filethumb"];
                    } else {
                        $imgURL = $this->getMediaUrl() . $_item["filethumb"];
                    }
                } else {
                    $imgURL = $this->getMediaUrl() . "mediaappearance/video_icon_full.jpg";
                }
            } else {
                if ($_item["filethumb"] != "") {
                    if (strpos($_item["filethumb"], 'https://') !== false) {
                        $imgURL =$_item["filethumb"];
                    } else {
                        $imgURL = $this->getMediaUrl() . $_item["filethumb"];
                    }
                } else {
                    $imgURL = $this->getMediaUrl() . "mediaappearance/video_icon_full.jpg";
                }
            }

            //For Video URL 
            if ($videoData !== false) {
                $video_type = $videoData['video_type'];
                $video_id = $videoData['video_id'];
                if ($video_type == "vimeo") {
                    $class = "fancybox";
                    $videoRel = "mediagal";
                    $videoURL = 'http://player.vimeo.com/' . $video_id . '?autoplay=1';
                } elseif ($video_type == "youtube") {
                    $class = "fancybox";
                    $videoRel = 'mediagal';
                    $videoURL = "http://www.youtube.com/watch?v=" . $video_id;
                } elseif ($video_type == "dailymotion") {
                    $class = "fancybox";
                    $videoRel = 'mediagal';
                    $videoURL = $video_id;
                }
            } else {
                $class = "fancybox";
                $videoURL = $_item["videourl"];
                $videoRel = 'mediagal';
            }
        } else {
            $videoRel = "mediagal";
            $videoURL = "#";
            $imgURL = $this->getMediaUrl() . "mediaappearance/video_icon_full.jpg";
        }
        $data['data_type'] = $data_type;
        $data['class'] = $class;
        $data['videoURL'] = $videoURL;
        $data['videoRel'] = $videoRel;
        $data['imgURL'] = $imgURL;

        return $data;
    }
    public function createtiles($_gimage)
    {
        //$html='';
        //exit;
       // return $html;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $block = $objectManager->create('FME\Mediaappearance\Block\Mediaappearance');
            $html='';
            //$targetPath = $this->getMediaUrl($_gimage["filethumb"]);
           // $thumbPath = $this->getThumbsDirPath($targetPath);
           // echo $thumbPath ;
           // $arrayName = explode('/', $_gimage["img_name"]);
           // $gallery_name = $_gimage['gal_name'];
            //$thumbnail_path =  $thumbPath . '/' . $arrayName[3];
            //echo $_gimage['img_label'] ;

            $image_path = $this->getthumblinkdata($_gimage["filethumb"]);
            $description = $_gimage["media_title"];
            
    

            $html.='  <div class="tile '.$block->addfilterwithtiles($_gimage["mediagallery_id"]).'  " >';
        /*if ($this->getMagniferOption()=="lighbox") {
            $html.='  <a class="tile-inner" href="'.$image_path.'"';
            $html.='data-title="'.$_gimage['img_label'].'"   data-lightbox="gallery">';
        } else {*/
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
           
        if ($this->enableCaption()) {
            if($_gimage['mediatype']==3||$_gimage['mediatype']=="3")
            {
            $html.=$block->addCaption($_gimage['media_title'],"image");
            } else {
                $html.=$block->addCaption($_gimage['media_title'],"video");
            }
        }

            $html.='  </a>';

            //Here we define the 
            if($_gimage['mediatype']==2||$_gimage['mediatype']=="2")
            {
                $html.='<div id="test-popup'.$_gimage['mediaappearance_id'].'" class="white-popup mfp-hide">';
                $html.='<video id="player1" width="750" height="421" controls preload="none">';
                $html.='<source src="'.$this->getMediaUrl().'mediaappearance/files/'.$_gimage['filename'].'" type="video/mp4">';
                $html.=' </video>';
                $html.='</div>';
            }
        /*    <div id="test-popup1" class="white-popup mfp-hide">
                <video id="player1" width="750" height="421" controls preload="none">
                    <source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/CastVideos/mp4/BigBuckBunny.mp4" type="video/mp4">
                </video>

                </div>*/
            //
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
    public function getYouTubeApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_YOUTUBE_API_KEY);
    }
    /**
     * splitImageValue
     * @param  $imageValue
     * @param  string $attr
     * @return string
     */
    public function splitImageValue($imageValue, $attr = "name")
    {
        $imArray = explode("/", $imageValue);

        $name = $imArray[count($imArray) - 1];
        $path = implode("/", array_diff($imArray, [$name]));
        if ($attr == "path") {
            return $path;
        } else {
            return $name;
        }
    }

    /**
     * getProductMedia
     * @param  $product_id
     * @return array
     */
    public function getCMSMedia($cms_id)
    {
        return $this->_mediaappearancemediaappearance->getCMSMedia($cms_id);
    }
    public function getProductMedia($product_id)
    {
        return $this->_mediaappearancemediaappearance->getProductMedia($product_id);
    }
    public function getCategoryMedia($cat_id)
    {
        return $this->_mediaappearancemediaappearance->getCategoryMedia($cat_id);
    
    }

    /**
     * getXml
     * @param  $position
     * @param  $id
     * @return string
     */
    public function getXml($position, $id)
    {
        if ($position == "main") {
            $xml = '<referenceContainer name="content"><block class="FME\Mediaappearance\Block\Mediablock" name="mediablock">
                              <action method="setBlockId">
                                <argument name="id" translate="true" xsi:type="string">' . $id . '</argument>
                              </action>
                            </block>
                            </referenceContainer>';
        } elseif ($position == "side") {
            $xml = '<referenceContainer name="sidebar.additional"><block class="FME\Mediaappearance\Block\Mediablock" name="mediablock">
                              <action method="setBlockId">
                                <argument name="id" translate="true" xsi:type="string">' . $id . '</argument>
                              </action>
                            </block>
                            </referenceContainer>';
        }

        return $xml;
    }
}
