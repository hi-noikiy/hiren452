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

class Cmsvideos extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var \FME\Mediaappearance\Helper\Data
     */
    public $_helper;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context                               $context
     * @param \Magento\Framework\ObjectManagerInterface                             $objectManager
     * @param \Magento\Framework\Registry                                           $registry
     * @param \FME\Mediaappearance\Helper\Data                                      $helper
     * @param \FME\Mediaappearance\Model\Resource\Mediaappearance\CollectionFactory $mediaappearancemediaappearanceFactory
     * @param \FME\Mediaappearance\Model\Mediaappearance                            $mediaappearancemediaappearance
     * @param array                                                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \FME\Mediaappearance\Helper\Data $helper,
        \FME\Mediaappearance\Model\ResourceModel\Mediaappearance\CollectionFactory $mediaappearancemediaappearanceFactory,
        \FME\Mediaappearance\Model\Mediaappearance $mediaappearancemediaappearance,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $context->getStoreManager();
        $this->_helper = $helper;
        $this->_mediaappearancemediaappearanceFactory = $mediaappearancemediaappearanceFactory;
        $this->_mediaappearancemediaappearance = $mediaappearancemediaappearance;
        parent::__construct($context, $data);
    }

    /**
     * function getCmsvideos
     * @return array
     */
    public function getCmsvideos()
    {
        $cmsHelper = $this->_objectManager->get('\Magento\Cms\Model\Page');
        $dataCurrentPage = $cmsHelper->getId();
       
        $collection = $this->_helper->getCMSMedia($dataCurrentPage); 
        
        //print_r($collection->getData());exit;
        /*$storeId = $this->_storeManager->getStore()->getId();
        
        $collection = $this->_mediaappearancemediaappearanceFactory->create()
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addFieldToFilter('cmspage_ids', [
                    ['finset' => [$dataCurrentPage]]])
                ->addFieldToFilter('main_table.status', 1);*/
        return $collection;
    }
}
