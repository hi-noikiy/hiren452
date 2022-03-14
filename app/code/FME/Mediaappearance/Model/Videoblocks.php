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

class Videoblocks extends \Magento\Framework\Model\AbstractModel
{

    protected $_objectManager;

    protected $_coreResource;

    /**
     * @param \Magento\Framework\Model\Context                           $context
     * @param \Magento\Framework\Registry                                $registry
     * @param \Magento\Framework\ObjectManagerInterface                  $objectManager
     * @param \Magento\Framework\App\Resource                            $coreResource
     * @param \FME\Mediaappearance\Model\Resource\Videoblocks            $resource
     * @param \FME\Mediaappearance\Model\Resource\Videoblocks\Collection $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $coreResource,
        \FME\Mediaappearance\Model\ResourceModel\Videoblocks $resource,
        \FME\Mediaappearance\Model\ResourceModel\Videoblocks\Collection $resourceCollection
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreResource = $coreResource;
        $this->_resourceCollection = $resourceCollection;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    public function _construct()
    {
        $this->_init('FME\Mediaappearance\Model\ResourceModel\Videoblocks');
    }
    
    /**
     * Retrieve related articles
     *
     * @return array
     */
    public function getRelatedMedia($blockId)
    {
            $mediavideoblockTable = $this->_coreResource->getTableName('media_block_videos');
            $mediaTable = $this->_coreResource->getTableName('fme_mediaappearance');
            
            $object =  $this->_objectManager->create('FME\Mediaappearance\Model\VideoblocksFactory');
            $collection = $object->create()->getCollection();
            $collection->addBlockIdFilter($blockId);
            $collection->addFieldToFilter('status', 1);
            $collection->getSelect()
            ->join(
                ['related' => $mediavideoblockTable],
                'main_table.media_block_id = related.media_block_id'
            )
             ->joinLeft(
                 ['media' => $mediaTable],
                 'related.mediaappearance_id = media.mediaappearance_id'
             )
             ->where("main_table.block_status = '1'")
            ->order('media.mediaappearance_id');
           // echo (string) $collection->getSelect(); exit;
            return $collection->getData();
    }
    
    /**
     * checkIdentifier
     * @param  $identifier
     * @param  $storeId
     * @return string
     */
    public function checkIdentifier($identifier, $storeId = null)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }
}