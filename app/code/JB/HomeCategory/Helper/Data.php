<?php

namespace JB\HomeCategory\Helper;


class Data extends \Magento\Framework\Url\Helper\Data
{


    public function __construct(
        \JB\AumikaFramework\Helper\Data $helper,
        \Magento\Framework\App\Helper\Context $context
    ) {
        // $this->_storeManager = $storeManagerInterface;
        $this->_helper = $helper;
        parent::__construct($context);
    }

    public function getHomeCat($name)
    {
        return $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );      
    }

    public function getCatImg1($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg2($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg3($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg4($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg5($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg6($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg7($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg8($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg9($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg10($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg11($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg12($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg13($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg14($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }

    public function getCatImg15($name){

        $catimg = $this->scopeConfig->getValue(
            'jb_homecategory/jbhomecategory/'.$name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = \JB\AumikaFramework\Model\Config\Backend\Image\ItemImage::UPLOAD_DIR;
        $path = $folderName . '/' . $catimg;
        $catImage = $this->_helper->getBaseUrl() . $path;

        return $catImage;
    }
}