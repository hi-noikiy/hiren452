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
namespace FME\Mediaappearance\Block\Adminhtml\Videoblocks\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Relatedmedia extends Extended
{
    
     /**
      * Set grid params
      *
      */
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\Product\LinkFactory
     */
    protected $_linkFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * __construct
     * @param \Magento\Backend\Block\Template\Context           $context
     * @param \Magento\Backend\Helper\Data                      $backendHelper
     * @param \FME\Mediaappearance\Model\MediaappearanceFactory $mediaappearancemediaappearanceFactory
     * @param \FME\Mediaappearance\Model\Mediaappearance        $mediaappearancemediaappearance
     * @param \FME\Mediaappearance\Model\Videoblocks            $videoblocks
     * @param \Magento\Framework\App\Resource                   $coreResource
     * @param \Magento\Framework\Registry                       $coreRegistry
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \FME\Mediaappearance\Model\MediaappearanceFactory $mediaappearancemediaappearanceFactory,
        \FME\Mediaappearance\Model\Mediaappearance $mediaappearancemediaappearance,
        \FME\Mediaappearance\Model\Videoblocks $videoblocks,
        \Magento\Framework\App\ResourceConnection $coreResource,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_mediaappearancemediaappearanceFactory = $mediaappearancemediaappearanceFactory;
        $this->_mediaappearancemediaappearance = $mediaappearancemediaappearance;
        $this->_videoblocks = $videoblocks;
        $this->_coreResource = $coreResource;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     */

    public function _construct()
    {
        parent::_construct();
        $this->setId('related_media_grid');
        $this->setDefaultSort('mediaappearance_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(['in_media'=>1]);
        }
    }
    
    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getMedia()
    {
        return $this->_coreRegistry->registry('current_block');
    }
    
    /**
     * Add filter
     *
     * @param object $column
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_media') {
            $mediaIds = $this->getSelectedRelatedMedia();

            if (empty($mediaIds)) {
                $mediaIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('mediaappearance_id', ['in'=>$mediaIds]);
            } else {
                if ($mediaIds) {
                    $this->getCollection()->addFieldToFilter('mediaappearance_id', ['nin'=>$mediaIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
    
        return $this;
    }
    
    /**
     * _prepareCollection
     * @return Collection
     */
    protected function _prepareCollection()
    {
                
        $collection = $this->_mediaappearancemediaappearanceFactory->create()->getCollection()
                          ->addOrder('mediaappearance_id', 'asc');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    /**
     * _prepareColumns
     * @return extended
     */
    protected function _prepareColumns()
    {

         $this->addColumn('in_media', [
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'in_media',
            'align'             => 'center',
            'values'            => $this->getSelectedRelatedMedia(),
            'index'             => 'mediaappearance_id'
         ]);

         $this->addColumn('mediaappearance_id', [
            'header'    => __('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'mediaappearance_id',
         ]);
          
         $this->addColumn('title', [
            'header'    => __('Title'),
            'align'     =>'left',
            'index'     => 'title',
         ]);
        
         $this->addColumn('status', [
            'header'    => __('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => [
              1 => 'Enabled',
              2 => 'Disabled',
            ],

         ]);

         $this->addColumn('position', [
            'header' => __('Position'),
                'type' => 'number',
                'index' => 'position',
                'editable' => 'false'
         ]);

         return parent::_prepareColumns();
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/RelatedmediaGrid', ['_current' => true]);
    }
    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
    
     /**
      * Retrieve related products
      *
      * @return array
      */
    public function getSelectedRelatedMedia()
    {
        $id = $this->getRequest()->getParam('id');
        $mediaArr = [];
        foreach ($this->_videoblocks->getRelatedMedia($id) as $media) {
            $mediaArr[$media["mediaappearance_id"]] = ['position' => '0'];
        }
        $media = array_keys($mediaArr);
        return $media;
    }
}
