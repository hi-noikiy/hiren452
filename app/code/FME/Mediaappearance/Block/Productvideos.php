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

class Productvideos extends \Magento\Framework\View\Element\Template
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
     * @param \Magento\Framework\Registry                                           $registry
     * @param \FME\Mediaappearance\Helper\Data                                      $helper
     * @param \FME\Mediaappearance\Model\Resource\Mediaappearance\CollectionFactory $mediaappearancemediaappearanceFactory
     * @param \FME\Mediaappearance\Model\Mediaappearance                            $mediaappearancemediaappearance
     * @param array                                                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \FME\Mediaappearance\Helper\Data $helper,
        \FME\Mediaappearance\Model\ResourceModel\Mediaappearance\CollectionFactory $mediaappearancemediaappearanceFactory,
        \FME\Mediaappearance\Model\Mediaappearance $mediaappearancemediaappearance,
        array $data = []
    ) {
        $this->_mediaappearancemediaappearanceFactory = $mediaappearancemediaappearanceFactory;
        $this->_mediaappearancemediaappearance = $mediaappearancemediaappearance;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $context->getStoreManager();
        $this->_helper = $helper;
        parent::__construct($context, $data);
        $this->setTabTitle();
    }

    /**
     * Function getCategoryvideos
     * @return array
     */
    public function getProductvideos()
    {

       // $collection=[];
        if ($this->_coreRegistry->registry('product')) {
        $id = $this->getProduct()->getId();

        $collection = $this->_helper->getProductMedia($id);
        }
        //print_r($this->_coreRegistry->registry('current_category')->getId());
       elseif ($this->_coreRegistry->registry('current_category')->getId()) {
            $cid = $this->_coreRegistry->registry('current_category')->getId();
            $collection = $this->_helper->getCategoryMedia($cid); 
        
        }
        //print_r($collection->getData());
        //exit;
        return $collection;
    }
    public function setTabTitle()
    {
       // $title = $this->_helper->getTitle();
        
        $title = "Product Videos";
        $this->setTitle(__($title));
    }
    public function getProduct()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product;
    }

    public function getHelper()
    {
        return $this->_helper;
    }
}
