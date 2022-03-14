<?php

namespace Hiddentechies\Reviewspro\Block\Adminhtml\Edit;

class Feedback extends \Magento\Backend\Block\Template
{
    protected $_reviewsproFactory;

    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Hiddentechies\Reviewspro\Model\ReviewsproFactory $reviewsproFactory
    )
    {
        $this->_reviewsproFactory = $reviewsproFactory;
        $this->setTemplate("review/feedback.phtml");
        parent::__construct($context);
    }

    
    public function getReviewFeedback()
    {
        $thisReviewproCollection = $this->_reviewsproFactory->create()
            ->getCollection()
            ->addFieldToFilter('review_id', $this->getRequest()->getParam('id'));

        return $thisReviewproCollection;
    }

}