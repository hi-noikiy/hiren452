<?php

namespace Hiddentechies\Reviewspro\Observer;

class AdminProductReviewDeleteBefore implements \Magento\Framework\Event\ObserverInterface
{
    
    protected $_request;

    protected $_reviewsproFactory;

    protected $_mediaDirectory;

    protected $_fileHandler;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $fileHandler,
        \Hiddentechies\Reviewspro\Model\ReviewsproFactory $reviewsproFactory
    )
    {
        $this->_request = $request;
        $this->_fileHandler = $fileHandler;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_reviewsproFactory = $reviewsproFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        $reviewId = $this->_request->getParam('id', false);
        if ($reviewId) {
            $this->deleteReviewMedia($reviewId);
            return;
        }

        $reviewIds = $this->_request->getParam('reviews', false);
        
        if ($reviewIds) {
            foreach ($reviewIds as $id) {
                $this->deleteReviewMedia($id);
            }
            return;
        }
    }

    private function deleteReviewMedia($reviewId)
    {
        $path = $this->_mediaDirectory->getAbsolutePath('review_customer_img');

        $thisReviewImgCollection = $this->_reviewsproFactory->create()
            ->getCollection()
            ->addFieldToFilter('review_id', $reviewId);

        foreach ($thisReviewImgCollection as $img) {

            $imageFile = $img->getReviewImg();
            if ($imageFile != '') {
                $imgUrl = $path . $imageFile;
                if ($this->_fileHandler->isExists($imgUrl)) {
                    $this->_fileHandler->deleteFile($imgUrl);
                }
            }
            $deletedReviewId = $img->getId();

            $reviewImageData = $this->_reviewsproFactory->create()->load($deletedReviewId);
            $reviewImageData->delete();
        }
    }
}
