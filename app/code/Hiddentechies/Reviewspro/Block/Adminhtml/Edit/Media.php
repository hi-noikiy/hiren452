<?php

namespace Hiddentechies\Reviewspro\Block\Adminhtml\Edit;

class Media extends \Magento\Backend\Block\Template
{
    protected $_reviewsproFactory;

    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Hiddentechies\Reviewspro\Model\ReviewsproFactory $reviewsproFactory
    )
    {
        $this->_reviewsproFactory = $reviewsproFactory;
        $this->setTemplate("review/img.phtml");
        parent::__construct($context);
    }

    
    public function getReviewImage()
    {
        $thisReviewproCollection = $this->_reviewsproFactory->create()
            ->getCollection()
            ->addFieldToFilter('review_id', $this->getRequest()->getParam('id'));

        return $thisReviewproCollection;
    }

    
    public function getReviewImgUrl()
    {
        $reviewproDirectoryPath = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'review_customer_img';

        return $reviewproDirectoryPath;
    }

}