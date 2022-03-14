<?php

namespace Hiddentechies\Reviewspro\Observer;

class ProductReviewSaveAfter implements \Magento\Framework\Event\ObserverInterface 
{
    protected $_request;
    protected $_reviewsproFactory;
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request, 
        \Magento\Framework\Filesystem $filesystem, 
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory, 
        \Hiddentechies\Reviewspro\Model\ReviewsproFactory $reviewsproFactory
    ) {
        $this->_request = $request;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_reviewsproFactory = $reviewsproFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $reviewId = $observer->getEvent()->getObject()->getReviewId();
        $media = $this->_request->getFiles('review_img');
        $target = $this->_mediaDirectory->getAbsolutePath('review_customer_img');
        $imageFilePath = '';
        
        if ($media) {
            try {
                $uploader = $this->_fileUploaderFactory->create(['fileId' => 'review_img']);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $result = $uploader->save($target);
                $imageFilePath = $result['file'];
            } catch (\Exception $e) {
                if ($e->getCode() == 0) {
                    $this->messageManager->addError("Something went wrong while saving review image.");
                }
            }
        }

        if ($reviewId) {
            $reviewImage = $this->_reviewsproFactory->create();
            $reviewImage->setReviewImg($imageFilePath);
            $reviewImage->setReviewId($reviewId);
            $reviewImage->save();
        }
    }

}
