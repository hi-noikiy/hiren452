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

class Mediaappearance extends \Magento\Framework\Model\AbstractModel
{

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const FEATURED_YES = 1;
    const FEATURED_NO = 0;

    protected $_objectManager;
    protected $_coreResource;

    /**
     * @param \Magento\Framework\Model\Context                               $context            [description]
     * @param \Magento\Framework\Registry                                    $registry           [description]
     * @param \Magento\Framework\ObjectManagerInterface                      $objectManager      [description]
     * @param \Magento\Framework\App\Resource                                $coreResource       [description]
     * @param \FME\Mediaappearance\Model\Resource\Mediaappearance            $resource           [description]
     * @param \FME\Mediaappearance\Model\Resource\Mediaappearance\Collection $resourceCollection [description]
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $coreResource,
        \FME\Mediaappearance\Model\ResourceModel\Mediaappearance $resource,
        \FME\Mediaappearance\Model\ResourceModel\Mediaappearance\Collection $resourceCollection
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreResource = $coreResource;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    /**
     * _construct
     *
     */
    public function _construct()
    {
        $this->_init('FME\Mediaappearance\Model\ResourceModel\Mediaappearance');
    }

    /**
     * getRelatedProducts
     * @param  $mediaappearanceId
     * @return array
     */
    public function getRelatedMediaVideo($mediagalleryId)
    {
        $mediaappearanceTable = $this->_coreResource
                ->getTableName('fme_mediaappearance');

        $collection = $this->_objectManager->create('FME\Mediaappearance\Model\Mediaappearance')
                ->getCollection()
                ->addFieldToFilter('main_table.mediagallery_id', $mediagalleryId);


        $collection->getSelect()
                ->joinLeft(['related' => $mediaappearanceTable], 'main_table.mediagallery_id = related.mediagallery_id')
                ->order('main_table.mediagallery_id');
               // echo $collection->getSelect();exit;
                //print_r($collection->getData());exit;
        return $collection->getData();
    }
    public function getRelatedProducts($mediaappearanceId)
    {

        $mediaappearanceTable = $this->_coreResource
                ->getTableName('fme_mediagallery_products');

        $collection = $this->_objectManager->create('FME\Mediaappearance\Model\Mediaappearance')
                ->getCollection()
                ->addFieldToFilter('main_table.mediagallery_id', $mediaappearanceId);


        $collection->getSelect()
                ->joinLeft(['related' => $mediaappearanceTable], 'main_table.mediagallery_id = related.mediagallery_id')
                ->order('main_table.mediagallery_id');
        return $collection->getData();
    }

    /**
     * getCMSPage
     * @return array
     */
    public function getCMSPage()
    {
        $CMSTable = $this->_coreResource->getTableName('cms_page');
        $sqry = "select title as label, page_id as value from " . $CMSTable . " where is_active=1";
        $connection = $this->_coreResource->getConnection('core_read');
        $select = $connection->query($sqry);
        return $rows = $select->fetchAll();
    }

    /**
     * getProductMedia
     * @param  $id
     * @return array
     */
    public function getProductMedia($id)
    {
        $mediaappearanceTable = $this->_coreResource
                ->getTableName('fme_mediagallery_products');

        $collection = $this->_objectManager->create('FME\Mediaappearance\Model\Mediaappearance')
                ->getCollection();


        $collection->getSelect()
                ->joinLeft(['related' => $mediaappearanceTable], 'main_table.mediagallery_id = related.mediagallery_id')
                ->where('related.product_id = '.$id .' and main_table.status = 1')
                ->order('main_table.mediagallery_id');

            //echo $collection->getSelect();
            //exit;
        return $collection;
    }

    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    public function getFeaturedStatuses()
    {
        return [self::FEATURED_YES => __('Yes'), self::FEATURED_NO => __('No')];
    }

    public function getProducts(\FME\Mediaappearance\Model\Mediaappearance $object)
    {
      ///echo $object->getId();exit;
              $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_mediagallery_products'))->where('mediagallery_id = ?', $object->getId());
        $data         = $this->_getResource()->getConnection()->fetchAll($select);
        if ($data) {
            $productsArr = [];
            foreach ($data as $_i) {
                $productsArr[] = $_i['product_id'];
            }

           // ////print_r($productsArr);exit;
            return $productsArr;
        }
    }


    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $array = $this->getData('products_position');
        if ($array === null) {
            $temp = $this->getData('product_id');
            
            if(isset($temp)):
                for ($i = 0; $i < sizeof($temp); $i++) {
                    $array[$temp[$i]] = 0;
                }
            endif;
            $this->setData('products_position', $array);
        }
        return $array;
    }//end getProductsPosition()
}
