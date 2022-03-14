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
namespace FME\Mediaappearance\Block\Adminhtml\Mediaappearance\Edit\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Video extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface {

    /**
     * @param \Magento\Backend\Block\Template\Context    $context      
     * @param \Magento\Framework\Registry                $registry     
     * @param \FME\Mediaappearance\Helper\Data           $helper       
     * @param \FME\Mediaappearance\Model\Mediaappearance $model        
     * @param array                                      $data         
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, 
            \FME\Mediaappearance\Helper\Data $helper, \FME\Mediaappearance\Model\Mediaappearance $model, array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registery = $registry;
        $this->_helper = $helper;
        $this->_storeManager = $context->getStoreManager();
        $this->_model = $model;
    }

    /**
     * render 
     * @param  AbstractElement $element 
     * @return html
     */
    public function render(AbstractElement $element) {

        $_val = $this->_registery->registry('mediagallery_data');
        $_Typevalfile = '';
        $_Typevalurl = '';
        $html = '';

        if ($_val["mediatype"] == '1') {
            $_Typevalfile = 'checked="checked"';
            $_Typevalurl = '';
        } elseif ($_val["mediatype"] == '2') {
            $_Typevalurl = 'checked="checked"';
            $_Typevalfile = '';
        } else {
            $_Typevalfile = 'checked="checked"';
            $_Typevalurl = '';
        }

        //Get the Current File
        try {
            $object = $this->_model->load($this->getRequest()->getParam('id'));
            $note = false;
            //Config For Popup Window
            $popupWidth = $this->_helper->getPopupWidth();
            $popupHeight = $this->_helper->getPopupHeight();
            $autoPlay = $this->_helper->getPopupAutoPlay();
            $playAgain = false;

            if ($object["mediatype"] == "1") {

                if ($object["filethumb"] != "") {
                    $imgURL = $this->_storeManager->getStore()->getBaseUrl(
                                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                            ) . $object["filethumb"];
                } else {
                    $imgURL = $this->getViewFileUrl("FME_Mediaappearance::images/video_icon_full.jpg");
                }
                $videoURL = $this->_storeManager->getStore()->getBaseUrl(
                                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                        ) . $object["filename"];
                $videoRel = 'shadowbox;height=' . $popupHeight . ';width=' . $popupWidth . ';options={flashVars:{skin: ' . 'mediaappearance/skin01.zip,autostart: ' . $autoPlay . '}}; handleOversize: "resize",';
                if ($object["filename"] != '') {
                    $html .='<input type="hidden" value="1" name="filenotemty">';
                } else {
                    $html .='<input type="hidden" value="0" name="filenotemty">';
                }
            } elseif ($object["mediatype"] == "2") {

                //For Thumb
                $videoURL = $object["videourl"];
                $videoData = $this->_helper->video_info($object["videourl"]);
                if ($videoData !== false) {
                    if (1 == 1) {
                        $imgURL = $this->_storeManager->getStore()->getBaseUrl(
                                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                                ) . $object["filethumb"];
                    } else {
                        if ($object["filethumb"] != "") {
                            $imgURL = $this->_storeManager->getStore()->getBaseUrl(
                                            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                                    ) . $object["filethumb"];
                        } else {
                            $imgURL = $this->getViewFileUrl("FME_Mediaappearance::images/video_icon_full.jpg");
                        }
                    }
                } else {
                    if ($object["filethumb"] != "") {
                        $imgURL = $this->_storeManager->getStore()->getBaseUrl(
                                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                                ) . $object["filethumb"];
                    } else {
                        $imgURL = $this->getViewFileUrl("FME_Mediaappearance::images/video_icon_full.jpg");
                    }
                }

                //For Video URL
                if ($videoData !== false) {
                    $video_type = $videoData['video_type'];
                    $video_id = $videoData['video_id'];
                    if ($video_type == "vimeo") {
                        $videoRel = "shadowbox;height=" . $popupHeight . ";width=" . $popupWidth . ";";
                        $videoURL = 'http://vimeo.com/moogaloop.swf?clip_id=' . $video_id . '&autoplay=' . $autoPlay . '';
                    } elseif ($video_type == "youtube") {
                        $videoRel = 'shadowbox;height=' . $popupHeight . ';width=' . $popupWidth . ';options={flashVars:{autostart: ' . $autoPlay . '}}';
                        $videoURL = "http://www.youtube.com/v/" . $video_id;
                    } elseif ($video_type == "dailymotion") {
                        $videoRel = 'shadowbox;  height=' . $popupHeight . ';width=' . $popupWidth . '; options={flashVars:{autostart: ' . $this->_helper->getPopupAutoPlay() . '}}';
                        $videoURL = $video_id;
                    }
                } else {
                    $videoURL = "" . $object["videourl"];
                    $videoRel = 'shadowbox;height=' . $popupHeight . ';width=' . $popupWidth . ';options={flashVars:{skin: ' . 'mediaappearance/skin01.zip,autostart: ' . $autoPlay . '}}';
                }
                if ($object["videourl"] != '') {
                    $html .='<input type="hidden" value="1" name="videonotempty">';
                } else {
                    $html .='<input type="hidden" value="0" name="videonotempty">';
                }
            }

            if ($object->getId()) {
                $mediatypevid = $object["mediatype"];
                $videoLink = '&nbsp;&nbsp;<a href="' . $videoURL . '"  rel="' . $videoRel . '" onclick="closeadmin()">View current file</a>';


                $imgSrc = '&nbsp;&nbsp;<a href="' . $imgURL . '" rel="' . $videoRel . '">View current file</a>';
            } else {
                $videoLink = "";
                $imgSrc = "";
            }
        } catch (\Exception $e) {
            $videoLink = "";
        }

        $html .= '<div data-ui-id="adminhtml-mediaappearance-edit-tab-form-0-fieldset-element-form-field-mediatype" class="admin__field field field-mediatype">
                    	<label data-ui-id="adminhtml-mediaappearance-edit-tab-form-0-fieldset-element-text-mediatype-label" for="page_mediatype" class="label admin__field-label">
                    		<span>Choose Video Type</span>
                    	</label>
            			<div class="admin__field-control control">
                			<input type="radio" ' . $_Typevalfile . ' onclick="checkRadios();" id="video_typefile" value="1" name="mediatype"><label for="video_typefile" class="inline">&nbsp;Media File</label>&nbsp;
							<input type="radio" id="video_typeurl" ' . $_Typevalurl . ' onclick="checkRadios();" value="2" name="mediatype"><label for="video_typeurl" class="inline">&nbsp;URL</label>&nbsp;
							<p class="nm"><small>(If you want to upload file select (<b>Media File</b>) if you want to put yourtube video or link of video select second option)</small></p>	
                		</div>
                    </div>';
        $html .= '<div id="video_file_block">';
        $html .= '<div data-ui-id="adminhtml-mediaappearance-edit-tab-form-0-fieldset-element-form-field-my_file_uploader" class="admin__field field field-my_file_uploader ">
                    	<label data-ui-id="adminhtml-mediaappearance-edit-tab-form-0-fieldset-element-text-my_file_uploader-label" for="page_my_file_uploader" class="label admin__field-label">
                    		<span>Video File</span>
                    	</label>
            			<div class="admin__field-control control">
                			<input type="file" value="" name="my_file_uploader" id="my_file_uploader" />' . $videoLink . '
                			<p class="nm"><small>(Supported Format FLV, MPEG, MP4, MP3)</small></p>
                		</div>
                    </div>';
        $html .= '</div>';

        $html .= '<div id="video_url_block" style="display:none">';
        $html .= '<div data-ui-id="adminhtml-mediaappearance-edit-tab-form-0-fieldset-element-form-field-videourl" class="admin__field field field-videourl">
                    	<label data-ui-id="adminhtml-mediaappearance-edit-tab-form-0-fieldset-element-text-videourl-label" for="page_videourl" class="label admin__field-label">
                    		<span>Video URL</span>
                    	</label>
            			<div class="admin__field-control control">
                			<input type="text" class="input-text admin__control-text" data-ui-id="adminhtml-mediaappearance-edit-tab-form-0-fieldset-element-text-videourl" value="' . $_val["videourl"] . '" name="videourl" id="videourl" />' . $videoLink . '
                			<p class="nm"><small>(In URL field out youtube or Vimeo URL OR complete path of video e.g http://www.domain.com/media/abc.flv)</small></p>
                		</div>
                    </div>';
        $html .= '</div>';
        $html .= '<div data-ui-id="adminhtml-mediaappearance-edit-tab-form-0-fieldset-element-form-field-my_thumb_uploader" class="admin__field field field-my_thumb_uploader">
                    	<label data-ui-id="adminhtml-mediaappearance-edit-tab-form-0-fieldset-element-text-my_thumb_uploader-label" for="page_my_thumb_uploader" class="label admin__field-label">
                    		<span>Media Thumb</span>
                    	</label>
            			<div class="admin__field-control control">
                				<input type="file" value="" data-ui-id="adminhtml-mediaappearance-edit-tab-form-0-fieldset-element-text-my_thumb_uploader" name="my_thumb_uploader" id="my_thumb_uploader">' . $imgSrc . '
                				<p class="nm"><small>(Supported Format JPEG, PNG, GIF)</small></p>
                		</div>
                    </div>';
        $html .= '<script type="text/javascript">';
        $html .= "var checkRadios = function(){
					if ($('video_typefile').checked){
						
						$('video_file_block').show();
						$('video_url_block').hide();
			
					} else if($('video_typeurl').checked) {
			
						$('video_url_block').show();
						$('video_file_block').hide();
					}
				}
				window.onload = function() {
					checkRadios();
				}";


        $html .= '</script>';
        return $html;
    }

}

// @codingStandardsIgnoreFile