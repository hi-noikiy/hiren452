<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog product form gallery content
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method \Magento\Framework\Data\Form\Element\AbstractElement getElement()
 */
namespace FME\Mediaappearance\Block\Adminhtml\Grid\Helper\Form\Gallery;

use Magento\Framework\App\ObjectManager;
use Magento\Backend\Block\Media\Uploader;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Backend\Block\DataProviders\ImageUploadConfig as ImageUploadConfigDataProvider;
use Magento\MediaStorage\Helper\File\Storage\Database;

/** 
 * Block for gallery content.
 */
class Content extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'FME_Mediaappearance::mediaappearance/gallery.phtml';

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $_mediaConfig;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @var ImageUploadConfigDataProvider
     */
    private $imageUploadConfigDataProvider;

    /**
     * @var Database
     */
    private $fileStorageDatabase;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param array $data
     * @param ImageUploadConfigDataProvider $imageUploadConfigDataProvider
     * @param Database $fileStorageDatabase
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \FME\Mediaappearance\Model\Media\ConfigPhotogallery $mediaConfig,
        \Magento\Framework\Registry $coreRegister,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = [],
        ImageUploadConfigDataProvider $imageUploadConfigDataProvider = null,
        Database $fileStorageDatabase = null
    ) {

        $this->_jsonEncoder = $jsonEncoder;
        $this->_mediaConfig = $mediaConfig;
        $this->_coreRegister = $coreRegister;
        $this->objectMgr = $objectManager;        
        parent::__construct($context, $data);
        $this->imageUploadConfigDataProvider = $imageUploadConfigDataProvider
            ?: ObjectManager::getInstance()->get(ImageUploadConfigDataProvider::class);
        $this->fileStorageDatabase = $fileStorageDatabase
            ?: ObjectManager::getInstance()->get(Database::class);
    }

    /**
     * Prepare layout.
     *
     * @return AbstractBlock
     */
    protected function _prepareLayout() 
    {
        $productMetadata = $this->objectMgr->create('\Magento\Framework\App\ProductMetadata');
        $version = $productMetadata->getVersion();
        
        if (version_compare($version, '2.3.5', '>=')){
            
            $this->addChild(
                'uploader',
                \Magento\Backend\Block\Media\Uploader::class,
                ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
            );
    
            $this->getUploader()->getConfig()->setUrl(
                $this->_urlBuilder->getUrl('mediaappearanceadmin/mediaappearance/upload')
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
    
            $this->_eventManager->dispatch('catalog_product_gallery_prepare_layout_dasd', ['block' => $this]);
            //print_r($this);exit;
            return parent::_prepareLayout();
    
        
        }

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
        
        // $this->imageUploadConfigDataProvider = $this->objectMgr::getInstance()->get(\Magento\Backend\Block\DataProviders\ImageUploadConfig::class);

        // $this->addChild(
        //     'uploader',
        //     \Magento\Backend\Block\Media\Uploader::class,
        //     ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
        // );
    
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
        $this->_eventManager->dispatch('Mediagallery_prepare_layout', ['block' => $this]);

        return parent::_prepareLayout();
 
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
