<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Controller\Adminhtml\Popups;

/**
 * Class Save
 */
class Save extends \Plumrocket\Newsletterpopup\Controller\Adminhtml\Popups
{
    /**
     * @var \Plumrocket\Newsletterpopup\Model\FormFieldFactory
     */
    protected $_formFieldFactory;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\MailchimpListFactory
     */
    protected $_mailchimpListFactory;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\Config\Source\MailchimpList
     */
    protected $_mailchimpListSource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\DateFactory
     */
    protected $_dateFilterFactory;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList\CollectionFactory
     */
    private $listCollectionFactory;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList
     */
    private $listResource;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Plumrocket\Newsletterpopup\Helper\Data $dataHelper
     * @param \Plumrocket\Newsletterpopup\Helper\Adminhtml $adminhtmlHelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Plumrocket\Newsletterpopup\Model\FormFieldFactory $formFieldFactory
     * @param \Plumrocket\Newsletterpopup\Model\MailchimpListFactory $mailchimpListFactory
     * @param \Plumrocket\Newsletterpopup\Model\Config\Source\MailchimpList $mailchimpListSource
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Stdlib\DateTime\Filter\DateFactory $dateFilterFactory
     * @param \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList\CollectionFactory $listCollectionFactory
     * @param \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList $listResource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\Newsletterpopup\Helper\Data $dataHelper,
        \Plumrocket\Newsletterpopup\Helper\Adminhtml $adminhtmlHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Plumrocket\Newsletterpopup\Model\FormFieldFactory $formFieldFactory,
        \Plumrocket\Newsletterpopup\Model\MailchimpListFactory $mailchimpListFactory,
        \Plumrocket\Newsletterpopup\Model\Config\Source\MailchimpList $mailchimpListSource,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime\Filter\DateFactory $dateFilterFactory,
        \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList\CollectionFactory $listCollectionFactory,
        \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList $listResource
    ) {
        $this->_formFieldFactory = $formFieldFactory;
        $this->_mailchimpListFactory = $mailchimpListFactory;
        $this->_mailchimpListSource = $mailchimpListSource;
        $this->_timezone = $timezone;
        $this->_dateFilterFactory = $dateFilterFactory;
        $this->listCollectionFactory = $listCollectionFactory;
        $this->listResource = $listResource;
        parent::__construct($context, $dataHelper, $adminhtmlHelper, $resource);
    }

    /**
     * @param \Magento\Rule\Model\AbstractModel $model
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
        $data = $request->getParams();
        $data = $this->_filterPostData($data);
        $model->loadPost($data);
    }

    /**
     * @param \Magento\Rule\Model\AbstractModel $model
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     */
    protected function _afterSave($model, $request)
    {
        $model->cleanCache();

        if ($id = $model->getId()) {
            if ($fieldsData = $request->getParam('signup_fields')) {
                $this->_saveFormFields($fieldsData, $id);
            }

            $this->saveIntegrationList($request->getParams(), $id);
            $model->generateThumbnail();
        }
    }

    /**
     * Prepare extended time fields that was passing in POST data
     *
     * @param $postData
     * @param $fieldName
     * @return array
     */
    protected function _prepareExtendedTime($postData, $fieldName)
    {
        if (isset($postData[$fieldName]) && is_array($postData[$fieldName])) {
            $offset = $this->_dataHelper->getOffsetFromExtendedTime($postData[$fieldName], $fieldName);
            $postData[$fieldName] = !$offset ? null : implode(',', $postData[$fieldName]);
        }

        return $postData;
    }

    /**
     * @param $postData
     * @return array
     */
    protected function _prepareIntegrationMode($postData)
    {
        if (empty($postData['integration_enable'])) {
            $postData['integration_enable'] = [];
        }

        if (empty($postData['integration_mode'])) {
            $postData['integration_mode'] = [];
        }

        $postData['integration_enable'] = json_encode($postData['integration_enable']);
        $postData['integration_mode'] = json_encode($postData['integration_mode']);

        return $postData;
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($postData)
    {
        $postData = $this->_prepareExtendedTime($postData, 'cookie_time_frame');
        $postData = $this->_prepareExtendedTime($postData, 'coupon_expiration_time');
        $postData = $this->_prepareIntegrationMode($postData);

        if (isset($postData['stores'])) {
            if (in_array(0, $postData['stores'])) {
                $postData['store_id'] = '0';
            } else {
                $postData['store_id'] = implode(',', $postData['stores']);
            }
        }

        if (isset($postData['entity_id']) && empty($postData['entity_id'])) {
            unset($postData['entity_id']);
        }

        // Prepare dates.
        if (!empty($postData['start_date'])) {
            $inputFilter = new \Zend_Filter_Input(
                ['start_date' => $this->_dateFilterFactory->create()],
                [],
                $postData
            );
            $postData = $inputFilter->getUnescaped();
        }

        if (!empty($postData['end_date'])) {
            $inputFilter = new \Zend_Filter_Input(
                ['end_date' => $this->_dateFilterFactory->create()],
                [],
                $postData
            );
            $postData = $inputFilter->getUnescaped();
        }

        if (!isset($postData['code']) && !empty($postData['code_base64'])) {
            $postData['code'] = base64_decode($postData['code_base64']);
        }
        if (!isset($postData['style']) && !empty($postData['style_base64'])) {
            $postData['style'] = base64_decode($postData['style_base64']);
        }

        if (isset($postData['rule']['conditions'])) {
            $postData['conditions'] = $postData['rule']['conditions'];
        }
        if (isset($postData['rule']['actions'])) {
            $postData['actions'] = $postData['rule']['actions'];
        }
        unset($postData['rule']);

        return $postData;
    }

    /**
     * @param $data
     * @param $popupId
     * @return bool
     */
    protected function _saveFormFields($data, $popupId)
    {
        if (!$popupId) {
            return false;
        }
        // Email is require field
        if (isset($data['email'])) {
            $data['email']['enable'] = 1;
        }
        // If Confirmation is enabled but Password not then enable Password
        if (isset($data['confirm_password'])
            && isset($data['confirm_password']['enable'])
            && isset($data['password'])
            && !isset($data['password']['enable'])
        ) {
            $data['password']['enable'] = 1;
        }

        $systemItemsKeys = $this->_dataHelper->getPopupFormFieldsKeys(0, false);
        $popupItems = $this->_dataHelper->getPopupFormFields($popupId, false);

        foreach ($systemItemsKeys as $name) {
            if (array_key_exists($name, $data)) {
                if (array_key_exists($name, $popupItems)) {
                    $field = $popupItems[$name];
                } else {
                    $field = $this->_formFieldFactory->create();
                    $field->setData('popup_id', $popupId);
                    $field->setData('name', $name);
                }
                $field->setData('label', $data[$name]['label']);
                $field->setData('enable', (int)isset($data[$name]['enable']));
                $field->setData('sort_order', (int)$data[$name]['sort_order']);
                $field->save();
            }
        }
        return true;
    }

    /**
     * @deprecated since version 3.3.0
     *
     * @param $data
     * @param $popupId
     * @return bool
     */
    protected function _saveMailChimpList($data, $popupId)
    {
        if (!$this->_adminhtmlHelper->isMaichimpEnabled()) {
            return false;
        }
        $collectionData = $this->_dataHelper->getPopupMailchimpList($popupId, false);
        $mailchimpList = $this->_mailchimpListSource->toOptionHash();

        foreach ($mailchimpList as $key => $name) {
            if (array_key_exists($key, $data)) {
                if (array_key_exists($key, $collectionData)) {
                    $list = $collectionData[$key];
                } else {
                    $list = $this->_mailchimpListFactory->create();
                    $list->setData('popup_id', $popupId);
                    $list->setData('name', $key);
                }
                $list->setData('label', $data[$key]['label']);
                $list->setData('enable', (int)isset($data[$key]['enable']));
                $list->setData('sort_order', (int)$data[$key]['sort_order']);
                $list->save();
            }
        }

        return true;
    }

    /**
     * @param $postData
     * @param $popupId
     * @return $this
     */
    private function saveIntegrationList($postData, $popupId)
    {
        if (! empty($postData['mailchimp_list'])) {
            $postData['integration_list']['mailchimp'] = is_array($postData['mailchimp_list'])
                ? $postData['mailchimp_list']
                : [];
        }

        if (! empty($postData['integration_list']) && is_array($postData['integration_list'])) {
            /** @var \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList\Collection $collection */
            $collection = $this->listCollectionFactory->create();
            $collection->addPopupFilter($popupId);
            $savedLists = $collection->getGroupedIntegrationLists();
            $insertAndUpdateData = [];

            foreach ($postData['integration_list'] as $integrationId => $postLists) {
                if (! is_array($postLists)) {
                    continue;
                }

                $insertAndUpdateData = array_merge(
                    $insertAndUpdateData,
                    $this->prepareInsertAndUpdateDataForIntegration($integrationId, $popupId, $postLists, $savedLists)
                );
            }

            $this->listResource->insertAndUpdateLists($insertAndUpdateData);
        }

        return $this;
    }

    /**
     * @param $integrationId
     * @param $popupId
     * @param array $postLists
     * @param array $savedLists
     * @return array
     */
    private function prepareInsertAndUpdateDataForIntegration(
        $integrationId,
        $popupId,
        array $postLists,
        array $savedLists
    ) {
        $insertAndUpdateData = [];

        foreach ($postLists as $listId => $postList) {
            $label = ! empty($postList['label']) ? (string)$postList['label'] : $listId;
            $enable = isset($postList['enable']) ? 1 : 0;
            $sortOrder = isset($postList['sort_order']) ? (int)$postList['sort_order'] : 0;
            $insertAndUpdateItemData = [
                'entity_id' => null,
                'popup_id' => $popupId,
                'integration_id' => $integrationId,
                'name' => (string)$listId,
                'label' => $label,
                'enable' => $enable,
                'sort_order' => $sortOrder,
            ];

            if (! empty($savedLists[$integrationId][$listId])) {
                $insertAndUpdateItemData['entity_id'] = $savedLists[$integrationId][$listId]['entity_id'];
            }

            $insertAndUpdateData[] = $insertAndUpdateItemData;
        }

        return $insertAndUpdateData;
    }
}
