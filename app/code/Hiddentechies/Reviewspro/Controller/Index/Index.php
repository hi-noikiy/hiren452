<?php

namespace Hiddentechies\Reviewspro\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action {

    public function execute() {

        $reviewId = $this->getRequest()->getPost('review_id');
        $feedbackType = $this->getRequest()->getPost('feedback_type');
        $feedback = $this->getRequest()->getPost('feedback');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reviewsproFactory = $objectManager->create('Hiddentechies\Reviewspro\Model\ReviewsproFactory');


        $reviewimgData = $this->checkReviewExist($reviewId);
        if (count($reviewimgData) > 0) {
            $reviewimgId = $reviewimgData['id'];
            $reviewPositive = $reviewimgData['review_positive'];
            $reviewNegative = $reviewimgData['review_negative'];

            if ($reviewimgId) {
                $reviewData = $reviewsproFactory->create()->load($reviewimgId);
                if ($feedbackType == 'positive') {
                    if ($feedback == 'inc') {
                        $reviewPositive = $reviewPositive + 1;
                    } else if ($feedback == 'dec') {
                        $reviewPositive = $reviewPositive - 1;
                    }
                    $reviewData->setReviewPositive($reviewPositive);
                } else if ($feedbackType == 'negative') {
                    if ($feedback == 'inc') {
                        $reviewNegative = $reviewNegative + 1;
                    } else if ($feedback == 'dec') {
                        $reviewNegative = $reviewNegative - 1;
                    }
                    $reviewData->setReviewNegative($reviewNegative);
                }
                $reviewData->save();
            } else {
                $reviewImage = $reviewsproFactory->create();
                if ($feedbackType == 'positive') {
                    if ($feedback == 'inc') {
                        $reviewPositive = $reviewPositive + 1;
                    } else if ($feedback == 'dec') {
                        $reviewPositive = $reviewPositive - 1;
                    }
                    $reviewImage->setReviewPositive($reviewPositive);
                } else if ($feedbackType == 'negative') {
                    if ($feedback == 'inc') {
                        $reviewNegative = $reviewNegative + 1;
                    } else if ($feedback == 'dec') {
                        $reviewNegative = $reviewNegative - 1;
                    }
                    $reviewImage->setReviewNegative($reviewNegative);
                }
                $reviewImage->setReviewId($reviewId);
                $reviewImage->save();
            }
        }
        echo 'success';
        exit;


        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    public function checkReviewExist($reviewId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reviewsproFactory = $objectManager->create('Hiddentechies\Reviewspro\Model\ReviewsproFactory');

        $reviewCollection = $reviewsproFactory->create()
                ->getCollection()
                ->addFieldToFilter('review_id', $reviewId);
        $reviewData = $reviewCollection->getData();
        if (count($reviewData) > 0) {
            return $reviewData[0];
        } else {
            return false;
        }
    }

}
