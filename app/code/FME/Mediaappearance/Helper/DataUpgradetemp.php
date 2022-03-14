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
namespace FME\Mediaappearance\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\Store;
use Magento\Store\Model\ScopeInterface;
class DataUpgrade extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_MEDIALIST_GALLERYTYPE= 'mediaappearance/medialist/gallerytype';



 
     //Media End
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \FME\Mediaappearance\Model\MediaappearanceFactory $mediaappearancemediaappearanceFactory,
        \FME\Mediaappearance\Model\Media $mediaappearancemediaappearance,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\App\ResourceConnection $coreResource
    ) {

        $this->_mediaappearancemediaappearanceFactory = $mediaappearancemediaappearanceFactory;
        $this->_mediaappearancemediaappearance = $mediaappearancemediaappearance;
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_eventManager = $context->getEventManager();
        $this->_imageFactory = $imageFactory;
        $this->_resource = $coreResource;

        parent::__construct($context);
    }
    public function getGalleryType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MEDIALIST_GALLERYTYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
