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
namespace FME\Mediaappearance\Model;

class Media extends \Magento\Framework\Model\AbstractModel
{
    protected $_objectManager;

    protected $_coreResource;

    protected $_storeManager;
    /**---Functions---*/
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $coreResource,
        \FME\Mediaappearance\Model\ResourceModel\Media $resource,
        \FME\Mediaappearance\Model\ResourceModel\Media\Collection $resourceCollection
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreResource = $coreResource;
        $this->_storeManager = $storeManager;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    public function _construct()
    {
        $this->_init('FME\Mediaappearance\Model\ResourceModel\Media');
    }
    public function getRelatedMediaVideoForModyfier($mediagalleryId)
    {
        $mediaappearanceTable = $this->_coreResource
                ->getTableName('fme_mediaappearance');

        $collection = $this->_objectManager->create('FME\Mediaappearance\Model\Mediaappearance')
                ->getCollection()
                ->addFieldToFilter('main_table.mediagallery_id', $mediagalleryId);


        $collection->getSelect()
                ->joinLeft(['related' => $mediaappearanceTable], 'main_table.mediagallery_id = related.mediagallery_id')
                ->where('related.mediatype in(1,2)')
                
                ->order('main_table.gorder');
                //echo $collection->getSelect();exit;
                //print_r($collection->getData());exit;
        return $collection->getData();
    }
    
    public function getRelatedMediaVideo($mediagalleryId)
    {
        $mediaappearanceTable = $this->_coreResource
                ->getTableName('fme_mediaappearance');

        $collection = $this->_objectManager->create('FME\Mediaappearance\Model\Mediaappearance')
                ->getCollection()
                
                ->addFieldToFilter('main_table.mediagallery_id', $mediagalleryId);


        $collection->getSelect()
                ->joinLeft(['related' => $mediaappearanceTable], 'main_table.mediagallery_id = related.mediagallery_id')
                ->where('related.mediatype in(1,2) and related.status = 1 and main_table.status = 1')
                
                ->order('main_table.mediagallery_id');
                //echo $collection->getSelect();exit;
                //print_r($collection->getData());exit;
        return $collection->getData();
    }
    
    public function getCMSMedia($id)
    {
        $mediaappearanceTable = $this->_coreResource
                ->getTableName('fme_mediagallery_cmspage');
        $mediagalleryTable = $this->_coreResource
                ->getTableName('fme_mediagallery');

        $mediagalleryStore = $this->_coreResource
        ->getTableName('fme_media_store');
        $collection = $this->_objectManager->create('FME\Mediaappearance\Model\Media')
                ->getCollection();

                $store_id=$this->_storeManager->getStore()->getId();
        $collection->getSelect()
        ->joinLeft(['related_gallery' => $mediagalleryTable], 'main_table.mediagallery_id = related_gallery.mediagallery_id')
        ->joinLeft(['related_store' => $mediagalleryStore], 'related_gallery.mediagallery_id = related_store.mediagallery_id')
                ->joinLeft(['related' => $mediaappearanceTable], 'main_table.mediagallery_id = related.mediagallery_id')
                ->where('related_store.store_id in (0,'.$store_id.') and related.cms_id = '.$id .' and main_table.status = 1 and related_gallery.status=1')
                ->order('main_table.mediagallery_id');

          //  echo $collection->getSelect();
           //exit;
        return $collection;
    }
    public function getProductMedia($id)
    {
        $mediaappearanceTable = $this->_coreResource
                ->getTableName('fme_mediagallery_products');
        $mediagalleryTable = $this->_coreResource
                ->getTableName('fme_mediagallery');
        $mediagalleryStore = $this->_coreResource
                ->getTableName('fme_media_store');
        $collection = $this->_objectManager->create('FME\Mediaappearance\Model\Media')
        ->getCollection();
        //->addStoreFilter($this->_storeManager->getStore()->getId());

        $store_id=$this->_storeManager->getStore()->getId();
        $collection->getSelect()
                ->joinLeft(['related_gallery' => $mediagalleryTable], 'main_table.mediagallery_id = related_gallery.mediagallery_id')
                ->joinLeft(['related_store' => $mediagalleryStore], 'related_gallery.mediagallery_id = related_store.mediagallery_id')
                
                ->joinLeft(['related' => $mediaappearanceTable], 'main_table.mediagallery_id = related.mediagallery_id')
                ->where('related_store.store_id in (0,'.$store_id.') and related.product_id = '.$id .' and main_table.status = 1 and related_gallery.status=1')
                ->order('main_table.mediagallery_id');

            //echo $collection->getSelect();
            //exit;
        return $collection;
    }
    public function getCategoryMedia($id)
    {
        $mediaappearanceTable = $this->_coreResource
                ->getTableName('fme_mediagallery_category');
        $mediagalleryTable = $this->_coreResource
                ->getTableName('fme_mediagallery');
                $mediagalleryStore = $this->_coreResource
                ->getTableName('fme_media_store');
        $collection = $this->_objectManager->create('FME\Mediaappearance\Model\Media')
                ->getCollection();
                $store_id=$this->_storeManager->getStore()->getId();

        $collection->getSelect()
                ->joinLeft(['related_gallery' => $mediagalleryTable], 'main_table.mediagallery_id = related_gallery.mediagallery_id')
                ->joinLeft(['related_store' => $mediagalleryStore], 'related_gallery.mediagallery_id = related_store.mediagallery_id')
                ->joinLeft(['related' => $mediaappearanceTable], 'main_table.mediagallery_id = related.mediagallery_id')
                ->where('related_store.store_id in (0,'.$store_id.') and related.category_id = '.$id .' and main_table.status = 1 and related_gallery.status=1')
                ->order('main_table.mediagallery_id');

           // echo $collection->getSelect();
            //exit;
        return $collection;
    }
    public function getRelatedMediaOnDrop($mediagalleryId,$parameter)
    {
        $mediaappearanceTable = $this->_coreResource
                ->getTableName('fme_mediaappearance');

        $collection = $this->_objectManager->create('FME\Mediaappearance\Model\Mediaappearance')
                ->getCollection()
                ->addFieldToFilter('main_table.mediagallery_id', $mediagalleryId);

        if(!($parameter=="video"|| $parameter=="images"))
        {
            $collection->getSelect()
                ->joinLeft(['related' => $mediaappearanceTable], 'main_table.mediagallery_id = related.mediagallery_id')
                ->where(' related.status = 1 and main_table.status = 1')
                
                ->order('main_table.gorder ASC');
        } else {
            
            if($parameter=="video")
            {
                $collection->getSelect()
                ->joinLeft(['related' => $mediaappearanceTable], 'main_table.mediagallery_id = related.mediagallery_id')
                ->where('related.mediatype in(1,2) and related.status = 1 and main_table.status = 1')
                
                ->order('main_table.gorder ASC');

            }
            else if($parameter=="images"){
                $collection->getSelect()
                ->joinLeft(['related' => $mediaappearanceTable], 'main_table.mediagallery_id = related.mediagallery_id')
                ->where('related.mediatype =3 and related.status = 1 and main_table.status = 1')
                
                ->order('main_table.gorder ASC');

            }


        }  
                //echo $collection->getSelect();exit;
                //print_r($collection->getData());exit;
        return $collection->getData();
    }
}
