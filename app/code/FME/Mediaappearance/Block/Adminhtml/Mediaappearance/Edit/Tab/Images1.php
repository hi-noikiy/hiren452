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
namespace FME\Mediaappearance\Block\Adminhtml\Mediaappearance\Edit\Tab;
use Magento\Backend\Block\Media\Uploader;
use Magento\Framework\View\Element\AbstractBlock;

class Images1 extends \Magento\Backend\Block\Widget
{

    /**
     * @var string
     */
    protected $_template = 'Magento_Catalog::catalog/product/helper/gallery.phtml';
  //  protected $_template = 'catalog/product/helper/gallery.phtml';

    //catalog/product/helper/gallery.phtml
    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $_mediaConfig;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;
    
    public $objectMgr;

    /**
     * @param \Magento\Backend\Block\Template\Context     $context
     * @param \Magento\Framework\Json\EncoderInterface    $jsonEncoder
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param array                                       $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \FME\Mediaappearance\Model\Media\ConfigPhotogallery $mediaConfig,
        \Magento\Framework\Registry $coreRegister,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {

        $this->_jsonEncoder = $jsonEncoder;
        $this->_mediaConfig = $mediaConfig;
        $this->_coreRegister = $coreRegister;
        $this->objectMgr = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'uploader',
            \Magento\Backend\Block\Media\Uploader::class,
            ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
        );

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->getUrl('catalog/product_gallery/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );

        $this->_eventManager->dispatch('catalog_product_gallery_prepare_layout', ['block' => $this]);

        return parent::_prepareLayout();
        



        $productMetadata = $this->objectMgr->create('\Magento\Framework\App\ProductMetadata');
        $version = $productMetadata->getVersion();
        
        if (version_compare($version, '2.3.1', '>=')){
            
            $this->imageUploadConfigDataProvider = $this->objectMgr::getInstance()->get(\Magento\Backend\Block\DataProviders\ImageUploadConfig::class);

            $this->addChild(
                'uploader',
                \Magento\Backend\Block\Media\Uploader::class,
                ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
            );
        
        }elseif (version_compare($version, '2.2.8', '>=') && version_compare($version, '2.3.0', '<')){
            
            $this->imageUploadConfigDataProvider = $this->objectMgr::getInstance()->get(\Magento\Backend\Block\DataProviders\UploadConfig::class);

            $this->addChild(
                'uploader',
                \Magento\Backend\Block\Media\Uploader::class,
                ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
            );
        
        }else{
            //this is for 2.3.0 and 2.2.7 or less
            $this->addChild('uploader', 'Magento\Backend\Block\Media\Uploader');        
        
        }
        
        $this->imageUploadConfigDataProvider = $this->objectMgr::getInstance()->get(\Magento\Backend\Block\DataProviders\ImageUploadConfig::class);

        $this->addChild(
            'uploader',
            \Magento\Backend\Block\Media\Uploader::class,
            ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
        );
    
        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->addSessionParam()->getUrl('mediaappearanceadmin/mediaappearance/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                    'images' => [
                        'label' => __('Images (.gif, .jpg, .png)'),
                        'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                    ],
                ]
        );
        $this->_eventManager->dispatch('catalog_product_gallery_prepare_layout', ['block' => $this]);

        return parent::_prepareLayout();
 
       
       
       
       /* $this->addChild('uploader', 'Magento\Backend\Block\Media\Uploader');

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->addSessionParam()->getUrl('mediaappearanceadmin/mediaappearance/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                    'images' => [
                        'label' => __('Images (.gif, .jpg, .png)'),
                        'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                    ],
                ]
        );

        $this->_eventManager->dispatch('catalog_product_gallery_prepare_layout', ['block' => $this]);

        return parent::_prepareLayout();
        */
    }

    public function images()
    {
        $images = $this->_coreRegister->registry('mediagallery_img');
        $img_data = $images->getData();
       // print_r($img_data);
        return $img_data;
    }

    /**
     * Retrieve uploader block
     *
     * @return Uploader
     */
    public function getUploader()
    {
        return $this->getChildBlock('uploader');
    }

    /**
     * Retrieve uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * @return string
     */
    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            __('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }

    /**
     * Retrieve media attributes
     *
     * @return array
     */
    public function getMediaAttributes()
    {
        return $this->getElement()->getDataObject()->getMediaAttributes();
    }

    /**
     * @return string
     */
    public function getImagesJson()
    {

        $value['images'] = $this->images();
   // print_r( $this->images());
     //  exit;
        if (is_array($value['images']) && count($value['images']) > 0) {
            foreach ($value['images'] as &$image) {
                $image['url'] = $this->_mediaConfig->getPhotogalleryMediaUrl($image['filethumb']);
                $image['file'] = $image['filethumb'];
                $image['label'] = $image['media_title'];
                //$image['tags'] = $image['tags'];
                //$image['width'] = $image['width'];
                //$image['height'] = $image['height'];
                $image['value_id'] = $image['mediaappearance_id'];
                $image['mediagallery_id'] = $image['mediagallery_id'];
                //$image['description'] = $image['img_description'];
            }
            return $this->_jsonEncoder->encode($value['images']);
        }

        return '[]';
    }

    /**
     * @return string
     */
    public function getImagesValuesJson()
    {
        $values = [];
        return $this->_jsonEncoder->encode($values);
    }

    /**
     * Get image types data
     *
     * @return array
     */
    public function getImageTypes()
    {
        $imageTypes = [];
        foreach ($this->images() as $attribute) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
            $imageTypes['image'] = [
                'code' => 'image',
                'value' => $attribute['filethumb'],
                'label' => $attribute['media_title'],
               // 'tags' => $attribute['tags'],
                'scope' => 'Store View',
                'name' => 'gallery[image]',
            ];
        }
        return $imageTypes;
    }

    /**
     * @return string
     */
    public function getImageTypesJson()
    {
        return $this->_jsonEncoder->encode($this->getImageTypes());
    }
}
