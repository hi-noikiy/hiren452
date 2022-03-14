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
namespace FME\Mediaappearance\Model\ResourceModel;

class Mediaappearance extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /*     * ---Functions--- */

    /**
     * Store model
     *
     * @var null|\Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Resource\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_objectManager = $objectManager;
    }

    public function _construct()
    {
        $this->_init('fme_mediagallery', 'mediagallery_id');
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {

        $select = $this->getConnection()->select()
                ->from($this->getTable('fme_media_store'))
                ->where('mediagallery_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $storesArray = [];
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            
            $object->setData('store_id', $storesArray);
        }



        $select = $this->getConnection()->select()
                ->from($this->getTable('fme_mediagallery_category'))
                ->where('mediagallery_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $storesArray = [];
            foreach ($data as $row) {
                $storesArray[] = $row['category_id'];
            }
            
            if ($storesArray) {
                $cms_idd = implode(',', $storesArray);
               // print_r($cms_idd);
                //exit;
                $object->setData('video_categories', $cms_idd);
            }
           // $object->setData('store_id', $storesArray);
        }


        //Get Category Ids
        $category_ids = $object->getData('category_ids');
        //print_r($category_ids);
       // exit;
        if ($category_ids != "") {
            $object->setData('category_ids', $category_ids);
        }

       /* $cms_ids = "1,2,3,4,5,6";
        echo"Cms Id ";
       //print_r( $cms_ids);
       // exit;
        if ($cms_ids != "") {
            $cmsPageIds = explode(",", $cms_ids);
            $result = array_unique($cmsPageIds);
            echo "CMS ID Resulr";

            //print_r($result);
            $object->setData('cmspage_id', $result);
        }*/

        $select = $this->getConnection()->select()
                ->from($this->getTable('fme_mediagallery_products'))
                ->where('mediagallery_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $productsArray = [];
            foreach ($data as $row) {
                $productsArray[] = $row['product_id'];
            }
            $object->setData('product_id', $productsArray);
        }
       /* $select = $this->getConnection()->select()
                ->from($this->getTable('fme_mediagallery_category'))
                ->where('mediagallery_id = ?', $object->getId());
      
        if ($data = $this->getConnection()->fetchAll($select)) {
            $productsArray = [];
            foreach ($data as $row) {
                $productsArray[] = $row['cmspage_id'];
            }
            //print_r($productsArray); //exit;
            $object->setData('cmspage_ids', $productsArray);
        }*/
        return parent::_afterLoad($object);
    }

    /**
     * _afterSave
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return extended
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
     // print_r($object->getData());
      //exit;
      //echo"asdasd";exit;
        $condition = $this->getConnection()->quoteInto('mediagallery_id = ?', $object->getId());
        //Get All Selected Categories
        $links = $object->getData("product_id");
        if (isset($links)) {
            $productIds = $links;
            $this->getConnection()->delete($this->getTable('fme_mediagallery_products'), $condition);

            foreach ($productIds as $_productId) {
                $productsArray = [];
                $productsArray['mediagallery_id'] = $object->getId();
                $productsArray['product_id'] = $_productId;
                $this->getConnection()->insert($this->getTable('fme_mediagallery_products'), $productsArray);
            }
        }

        $stores = $object->getData("store_id");
        if (isset($stores)) {
            $this->getConnection()->delete($this->getTable('fme_media_store'), $condition);
            foreach ($stores as $store) {
                $storeArray = [];
                $storeArray['mediagallery_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->getConnection()->insert($this->getTable('fme_media_store'), $storeArray);
            }
        }
        
        $stores = $object->getData("mediagallery_categories");
        if (isset($stores)) {
           
        
            $this->getConnection()->delete($this->getTable('fme_mediagallery_category'), $condition);
            foreach ($stores as $store) {
                $storeArray = [];
                $storeArray['mediagallery_id'] = $object->getId();
                $storeArray['category_id'] = $store;
                $this->getConnection()->insert($this->getTable('fme_mediagallery_category'), $storeArray);
            }
        }
        $stores = $object->getData("cmspage_ids");
        if (isset($stores)) {
            $this->getConnection()->delete($this->getTable('fme_mediagallery_cmspage'), $condition);
            foreach ($stores as $store) {
                $storeArray = [];
                $storeArray['mediagallery_id'] = $object->getId();
                $storeArray['cms_id'] = $store;
                $this->getConnection()->insert($this->getTable('fme_mediagallery_cmspage'), $storeArray);
            }
        }
        //cmspage_ids

        return parent::_afterSave($object);
    }
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        $data= $object->getData();
        
       //print_r($data);//exit;
       if(isset($data['data']))
       {
        $condition = $this->getConnection()->quoteInto('mediagallery_id = ? AND mediatype in (1,2)', $object->getId());
       // print_r($condition);exit;
        
        $this->getConnection()->delete($this->getTable('fme_mediaappearance'), $condition);
       } 
       if(isset($data['gallery']))
       {
        $condition = $this->getConnection()->quoteInto('mediagallery_id = ? AND mediatype=3', $object->getId());
        $this->getConnection()->delete($this->getTable('fme_mediaappearance'), $condition);
       }

        if(isset($data['data']))
       {
        $mediadata=$data['data']['product']['attachments'];
        $_photos_info=$mediadata['dynamic_rows'];
        
        $stores =$object->getData("mediagallery_id");
        if (isset($stores)) {
            foreach ($_photos_info as $store) {
                $storeArray = [];
                $storeArray['mediagallery_id'] = $object->getId();
                $storeArray['media_title'] = $store['title'];
               // $storeArray['videourl'] = $object->getData("link_url");
                $storeArray['featured_media'] =$store['featured'];
                $storeArray['status'] =$store['status'];
                $mediatype=0;
                $url="";
                $filename="";
                if($store['type']=='url' ){
                    $storeArray['mediatype']=1;
                     $storeArray['videourl']=$store['link_url'];
                }else{
                    $storeArray['mediatype']=2;
                    $storeArray['filename']=$store['filename'][0]['file'];
                }
                if(isset($store['filethumb'][0]['file']))
                {
                    $storeArray['filethumb']=$store['filethumb'][0]['file'];
                }else{
                    $storeArray['filethumb']=$store['filethumb'][0]['name'];
                }
               // $storeArray['filethumb'] = $store['filethumb'][0]['file'];
                
                                       
                
                
                
                //$storeArray['cms_id'] = $object->getData("filethumb")[0]['file'];
                //$storeArray['cms_id'] = $object->getData("sort_order");
                //$storeArray['cms_id'] = $object->getData("sort_order");
                $this->getConnection()->insert($this->getTable('fme_mediaappearance'), $storeArray);
            
                }
                //$this->getConnection()->insert($this->getTable('fme_mediaappearance'), $storeArray);
            
            }
        }
        
        if(isset($data['gallery']))
        {
            $mediadata=$data['gallery'];
            $_photos_info=$mediadata['images'];
        
        // print_r($_photos_info);exit;
            $stores =$object->getData("mediagallery_id");
            if (isset($stores)) {
                foreach ($_photos_info as $store) {
                    
                    if($store['removed']!=1)
                    {
                        $storeArray = [];
                    $storeArray['mediagallery_id'] = $object->getId();
                    $storeArray['media_title'] = $store['label'];
                    //$storeArray['featured_media'] =$store['featured'];
                    $storeArray['status'] =1;
                    $storeArray['mediatype']=3;
                    $storeArray['videourl']='';
                   //$storeArray['filename']=$store['file'];
                   $imagename=$store['file'];
                   //Identical operator === is not used for testing the return value of strpos function
                   if(strpos($store['file'],'.tmp')!== FALSE)
                   {
                     $imagename=substr($store['file'], 0, -4) . '';
                     }
                   $storeArray['filethumb'] = $imagename;
                    $this->getConnection()->insert($this->getTable('fme_mediaappearance'), $storeArray);
                  }
                }
            }
            
        }
       
        return parent::_beforeSave($object);
        
    //exit;

    }
}
