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
namespace FME\Mediaappearance\Controller\Adminhtml\Mediaappearance;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;

class Uploadvideo extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param \Magento\Backend\App\Action\Context             $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
       // \FME\Mediaappearance\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->_imageFactory = $imageFactory;
      //  $this->_helper = $helper;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Mediaappearance::fmeextensions_mediaappearance_items');
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {

        //echo "asdasdas";exit;



        try {
            $result = $this->addvideomedia();
            //print_r($result);exit;
            $result['cookie'] = [
                                 'name'     => $this->_getSession()->getName(),
                                 'value'    => $this->_getSession()->getSessionId(),
                                 'lifetime' => $this->_getSession()->getCookieLifetime(),
                                 'path'     => $this->_getSession()->getCookiePath(),
                                 'domain'   => $this->_getSession()->getCookieDomain(),
                                ];
                                
        } catch (\Exception $e) {
            $result = [
                       'error'     => "Video size must be of 2 Mb  or less than 2 Mb",
                       'errorcode' => $e->getCode(),
                      ];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);











        try {
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'filename']
            );
           // $uploader = $this->uploaderFactory->create(['fileId' => "filename"]);
            $uploader->setAllowedExtensions(['mp4','mp3','flv']);
            $uploader->setAllowRenameFiles(true);

            //$result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
            //echo "asdasdas";exit;
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
               // echo "asdasda1s";exit;
            $config = $this->_objectManager->get('FME\Mediaappearance\Model\Media\ConfigPhotogallery');
            $result = $uploader->save($mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath()));
           // echo $config->getPhotogalleryBaseTmpMediaPath();exit;
          //  echo "asdasdasave";exit;
        } catch (\Exception $e) {
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'filethumb']
            );
          //  $uploader = $this->uploaderFactory->create(['fileId' => 'filethumb']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
 
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
            $config = $this->_objectManager->get('FME\Mediaappearance\Model\Media\ConfigPhotogallery');
            $result = $uploader->save($mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath()));
            //$result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
      
            $flag     = 1;
        }










        if (!$result) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        /*
            * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
         */
      
        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path']     = str_replace('\\', '/', $result['path']);
        
          
       /* $result['url']      = $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ).$this->getFilePath($baseTmpPath, $result['file']);

        */
        $result['url'] = $this->_objectManager->get('FME\Mediaappearance\Model\Media\ConfigPhotogallery')
                ->getPhotogalleryTmpMediaUrl($result['file']);
        $result['name']     = $result['file'];
        //print_r($result);
       // exit;
        if (isset($result['file'])) {
            try {
                $config = $this->_objectManager->get('FME\Mediaappearance\Model\Media\ConfigPhotogallery');
            
                $relativePath = rtrim($config->getPhotogalleryBaseTmpMediaPath(), '/').'/'.ltrim($result['file'], '/');
                //print_r($relativePath);
              //exit;
              $coreFileStorageDatabase=$this->_objectManager->get('Magento\MediaStorage\Helper\File\Storage\Database');
            
              $coreFileStorageDatabase->saveFile($relativePath);
    
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }
       // echo"asdasd ";exit;
        return $result;
    }
    public function addvideomedia()
    {
        try {
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'filename']
            );
           // $uploader = $this->uploaderFactory->create(['fileId' => "filename"]);
            $uploader->setAllowedExtensions(['mp4','mp3','flv']);
            $uploader->setAllowRenameFiles(true);

            //$result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
            //echo "asdasdas";exit;
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
               // echo "asdasda1s";exit;
            $config = $this->_objectManager->get('FME\Mediaappearance\Model\Media\ConfigPhotogallery');
            $result = $uploader->save($mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath()));
           // echo $config->getPhotogalleryBaseTmpMediaPath();exit;
          //  echo "asdasdasave";exit;
        } catch (\Exception $e) {
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'filethumb']
            );
          //  $uploader = $this->uploaderFactory->create(['fileId' => 'filethumb']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
 
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
            $config = $this->_objectManager->get('FME\Mediaappearance\Model\Media\ConfigPhotogallery');
            $result = $uploader->save($mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath()));
            //$result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
      
            $flag     = 1;
        }










        if (!$result) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        /*
            * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
         */
      
        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path']     = str_replace('\\', '/', $result['path']);
        
          
       /* $result['url']      = $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ).$this->getFilePath($baseTmpPath, $result['file']);

        */
        $result['url'] = $this->_objectManager->get('FME\Mediaappearance\Model\Media\ConfigPhotogallery')
                ->getPhotogalleryTmpMediaUrl($result['file']);
        $result['name']     = $result['file'];
        //print_r($result);
       // exit;
        if (isset($result['file'])) {
            try {
                $config = $this->_objectManager->get('FME\Mediaappearance\Model\Media\ConfigPhotogallery');
            
                $relativePath = rtrim($config->getPhotogalleryBaseTmpMediaPath(), '/').'/'.ltrim($result['file'], '/');
                //print_r($relativePath);
              //exit;
              $coreFileStorageDatabase=$this->_objectManager->get('Magento\MediaStorage\Helper\File\Storage\Database');
            
              $coreFileStorageDatabase->saveFile($relativePath);
    
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }
       // echo"asdasd ";exit;
        return $result;
    }

    public function hexToRgb($hex, $alpha = false)
    {
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['0'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['1'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['2'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ($alpha) {
            $rgb['a'] = $alpha;
        }
        return $rgb;
    }
    public function resizeFile($source, $keepRation = true, $keepFrame = true, $fileName)
    {
        if (!is_file($source) || !is_readable($source)) {
            return false;
        }
        $targetDir = $this->getThumbsPath($source);
        
        
        //$width = $this->_helper->getThumbWidth();
        //$height = $this->_helper->getThumbHeight();
       // $bgColor = $this->_helper->getBgcolor();
       // $bgColorArray = $this->hexToRgb($bgColor);

        //$bgColorArray = explode(",", $bgColor);
        $imageObj = $this->_imageFactory->create($source);
        $imageObj->constrainOnly(true);
        $imageObj->keepAspectRatio($keepRation);
        $imageObj->keepFrame($keepFrame);
       // $imageObj->backgroundColor([intval($bgColorArray[0]),intval($bgColorArray[1]),intval($bgColorArray[2])]);
       // $imageObj->resize($width, $height);
        $dest = $targetDir . '/' . pathinfo($source, PATHINFO_BASENAME);
        $imageObj->save($dest);
       
        if (is_file($dest)) {
            return $dest;
        }
        return false;
    }


    /**
     * Return thumbnails directory path for file/current directory
     *
     * @param  string $filePath Path to the file
     * @return string
     */
    public function getThumbsPath($filePath = false)
    {
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
        $config = $this->_objectManager->get('FME\Mediaappearance\Model\Media\ConfigPhotogallery');
        $mediaRootDir = $mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath());
        $thumbnailDir = $mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath());
        if ($filePath && strpos($filePath, $mediaRootDir) === 0) {
            $thumbnailDir .= dirname(substr($filePath, strlen($mediaRootDir)));
        }
        $thumbnailDir .= '/'."thumb";
        return $thumbnailDir;
    }
}
