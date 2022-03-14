<?php

namespace Hiddentechies\Reviewspro\Observer;

class AdminProductReviewSaveAfter implements \Magento\Framework\Event\ObserverInterface {

    protected $_request;
    protected $_reviewsproFactory;
    protected $_mediaDirectory;
    protected $_fileHandler;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request, 
        \Magento\Framework\Filesystem $filesystem, 
        \Magento\Framework\Filesystem\Driver\File $fileHandler, 
        \Hiddentechies\Reviewspro\Model\ReviewsproFactory $reviewsproFactory
    ) {
        $this->_request = $request;
        $this->_fileHandler = $fileHandler;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_reviewsproFactory = $reviewsproFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $path = $this->_mediaDirectory->getAbsolutePath('review_customer_img');
        $deletedImg = $this->_request->getParam('deleted_img');
        $reviewId = $this->_request->getParam('id');
        $reviewTestimonial = $this->_request->getParam('review_testimonial');

        if ($deletedImg) {
            $reviewImageData = $this->_reviewsproFactory->create()->load($deletedImg);
            $imgUrl = $path . $reviewImageData->getReviewImg();
            if ($this->_fileHandler->isExists($imgUrl)) {
                $this->_fileHandler->deleteFile($imgUrl);
            }
            $reviewImageData->setReviewImg('');
            $reviewImageData->save();
        }
        if ($reviewId != '' && $reviewTestimonial != '') {
            $reviewimgId = $this->checkReviewExist($reviewId);
            if ($reviewimgId) {
                $reviewData = $this->_reviewsproFactory->create()->load($reviewimgId);
                $reviewData->setReviewTestimonial($reviewTestimonial);
                $reviewData->save();
            } else {
                $reviewImage = $this->_reviewsproFactory->create();
                $reviewImage->setReviewTestimonial($reviewTestimonial);
                $reviewImage->setReviewId($reviewId);
                $reviewImage->save();
            }
        }
    }

    public function checkReviewExist($reviewId) {
        $reviewCollection = $this->_reviewsproFactory->create()
                ->getCollection()
                ->addFieldToFilter('review_id', $reviewId);
        $reviewData = $reviewCollection->getData();
        if (count($reviewData) > 0) {
            return $reviewData[0]['id'];
        } else {
            return false;
        }
    }
}
