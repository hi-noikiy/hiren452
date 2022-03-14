<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Adminhtml Review Edit Form
 */

namespace Hiddentechies\Reviewspro\Block\Adminhtml\Edit;

class Form extends \Magento\Review\Block\Adminhtml\Edit\Form {

    protected function _prepareForm() {
        $review = $this->_coreRegistry->registry('review_data');
        $reviewTempId = $this->getRequest()->getParam('id');
        if ($reviewTempId) {
            $reviewTestimonialStatus = $this->getCurrentTestimonialStatus($reviewTempId);
            $review->setReviewTestimonial($reviewTestimonialStatus);
        }
        $product = $this->_productFactory->create()->load($review->getEntityPkValue());

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
                [
                    'data' => [
                        'id' => 'edit_form',
                        'action' => $this->getUrl(
                                'review/*/save', [
                            'id' => $this->getRequest()->getParam('id'),
                            'ret' => $this->_coreRegistry->registry('ret')
                                ]
                        ),
                        'method' => 'post',
                    ],
                ]
        );

        $fieldset = $form->addFieldset(
                'review_details', ['legend' => __('Review Details'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
                'product_name', 'note', [
            'label' => __('Product Name'),
            'text' => '<a href="' . $this->getUrl(
                    'catalog/product/edit', ['id' => $product->getId()]
            ) . '" onclick="this.target=\'blank\'">' . $this->escapeHtml(
                    $product->getName()
            ) . '</a>'
                ]
        );

        try {
            $customer = $this->customerRepository->getById($review->getCustomerId());
            $customerText = __(
                    '<a href="%1" onclick="this.target=\'blank\'">%2 %3</a> <a href="mailto:%4">(%4)</a>', $this->getUrl('customer/index/edit', ['id' => $customer->getId(), 'active_tab' => 'review']), $this->escapeHtml($customer->getFirstname()), $this->escapeHtml($customer->getLastname()), $this->escapeHtml($customer->getEmail())
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $customerText = ($review->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) ? __('Administrator') : __('Guest');
        }

        $fieldset->addField('customer', 'note', ['label' => __('Author'), 'text' => $customerText]);

        $fieldset->addField(
                'summary-rating', 'note', [
            'label' => __('Summary Rating'),
            'text' => $this->getLayout()->createBlock(
                    \Magento\Review\Block\Adminhtml\Rating\Summary::class
            )->toHtml()
                ]
        );

        $fieldset->addField(
                'detailed-rating', 'note', [
            'label' => __('Detailed Rating'),
            'required' => true,
            'text' => '<div id="rating_detail">' . $this->getLayout()->createBlock(
                    \Magento\Review\Block\Adminhtml\Rating\Detailed::class
            )->toHtml() . '</div>'
                ]
        );

        $fieldset->addField(
                'status_id', 'select', [
            'label' => __('Status'),
            'required' => true,
            'name' => 'status_id',
            'values' => $this->_reviewData->getReviewStatusesOptionArray()
                ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->hasSingleStore()) {
            $field = $fieldset->addField(
                    'select_stores', 'multiselect', [
                'label' => __('Visibility'),
                'required' => true,
                'name' => 'stores[]',
                'values' => $this->_systemStore->getStoreValuesForForm()
                    ]
            );
            $renderer = $this->getLayout()->createBlock(
                    \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element::class
            );
            $field->setRenderer($renderer);
            $review->setSelectStores($review->getStores());
        } else {
            $fieldset->addField(
                    'select_stores', 'hidden', ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $review->setSelectStores($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
                'nickname', 'text', ['label' => __('Nickname'), 'required' => true, 'name' => 'nickname']
        );

        $fieldset->addField(
                'title', 'text', ['label' => __('Summary of Review'), 'required' => true, 'name' => 'title']
        );

        $fieldset->addField(
                'detail', 'textarea', ['label' => __('Review'), 'required' => true, 'name' => 'detail', 'style' => 'height:24em;']
        );

        $fieldset->addField(
                'review_testimonial', 'select', [
            'label' => __('Display As Testimonial'),
            'name' => 'review_testimonial',
            'values' => $this->getTestimonialStatusOptionArray()
                ]
        );

        $fieldset->addField(
                'review_feedback', 'note', [
            'label' => __('Review Feedback'),
            'text' => $this->getLayout()->createBlock(
                    \Hiddentechies\Reviewspro\Block\Adminhtml\Edit\Feedback::class
            )->toHtml()
                ]
        );

        $fieldset->addField(
                'review_img', 'note', [
            'label' => __('Review Image'),
            'text' => $this->getLayout()->createBlock(
                    \Hiddentechies\Reviewspro\Block\Adminhtml\Edit\Media::class
            )->toHtml()
                ]
        );

        $fieldset->addField(
                'deleted_img', 'text', [
            'name' => 'deleted_img',
            'style' => 'visibility:hidden;'
                ]
        );

        $form->setUseContainer(true);
        $form->setValues($review->getData());
        $this->setForm($form);
        return \Magento\Backend\Block\Widget\Form::_prepareForm();
    }

    public function getCurrentTestimonialStatus($reviewId = 0) {
        if ($reviewId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $reviewsproFactory = $objectManager->create('Hiddentechies\Reviewspro\Model\ReviewsproFactory');
            $reviewCollection = $reviewsproFactory->create()->getCollection()
                    ->addFieldToFilter('review_id', $reviewId);
            $reviewData = $reviewCollection->getData();
            if (count($reviewData) > 0) {
                return $reviewData[0]['review_testimonial'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getTestimonialStatus() {
        return [
            0 => __('No'),
            1 => __('Yes')
        ];
    }

    public function getTestimonialStatusOptionArray() {
        $result = [];
        foreach ($this->getTestimonialStatus() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }

}
