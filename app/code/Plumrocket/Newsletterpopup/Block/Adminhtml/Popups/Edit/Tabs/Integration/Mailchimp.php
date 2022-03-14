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

namespace Plumrocket\Newsletterpopup\Block\Adminhtml\Popups\Edit\Tabs\Integration;

use Magento\Backend\Block\Widget\Form\Element\Dependence as ElementDependence;
use Plumrocket\Newsletterpopup\Block\Adminhtml\Popups\Edit\Renderer\InputTable as InputTableBlock;
use Plumrocket\Newsletterpopup\Block\Adminhtml\Popups\Edit\Renderer\Label as ExtendedLabel;

/**
 * Class Mailchimp
 */
class Mailchimp extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $sourceYesNo;

    /**
     * @var \Plumrocket\Newsletterpopup\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\Newsletterpopup\Helper\Adminhtml
     */
    private $adminhtmlHelper;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\Config\Source\MailchimpList
     */
    private $sourceMailchimpList;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\Config\Source\SubscriptionMode
     */
    private $sourceSubscriptionMode;

    /**
     * Mailchimp constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $sourceYesNo
     * @param \Plumrocket\Newsletterpopup\Helper\Data $dataHelper
     * @param \Plumrocket\Newsletterpopup\Helper\Adminhtml $adminhtmlHelper
     * @param \Plumrocket\Newsletterpopup\Model\Config\Source\MailchimpList $sourceMailchimpList
     * @param \Plumrocket\Newsletterpopup\Model\Config\Source\SubscriptionMode $sourceSubscriptionMode
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $sourceYesNo,
        \Plumrocket\Newsletterpopup\Helper\Data $dataHelper,
        \Plumrocket\Newsletterpopup\Helper\Adminhtml $adminhtmlHelper,
        \Plumrocket\Newsletterpopup\Model\Config\Source\MailchimpList $sourceMailchimpList,
        \Plumrocket\Newsletterpopup\Model\Config\Source\SubscriptionMode $sourceSubscriptionMode,
        array $data = []
    ) {
        $this->sourceYesNo = $sourceYesNo;
        $this->dataHelper = $dataHelper;
        $this->adminhtmlHelper = $adminhtmlHelper;
        $this->sourceMailchimpList = $sourceMailchimpList;
        $this->sourceSubscriptionMode = $sourceSubscriptionMode;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (! $this->adminhtmlHelper->isMaichimpEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     */
    protected function _prepareForm()
    {
        /** @var null|\Plumrocket\Newsletterpopup\Model\Popup $popup */
        $popup = $this->_coreRegistry->registry('current_model');
        $disabled = ! $this->adminhtmlHelper->isMaichimpEnabled();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('popup_');

        /* Add new fieldset */
        $fieldset = $form->addFieldset('mailchimp_fieldset', ['legend' => __('Mailchimp')]);

        /* Add invisible notice label */
        $fieldset->addType('extended_label', ExtendedLabel::class);
        $fieldset->addField('mailchimp_label', 'extended_label', [
            'hidden' => true,
        ]);
        $popup->setData('mailchimp_label', $this->getNoticeText());

        /* Add Enable Integration switcher */
        $elementId = 'integration_enable[mailchimp]';
        $value = $popup->getPreparedIntegrationEnable('mailchimp');
        $popup->setData($elementId, $value);

        $enable = $fieldset->addField(
            $elementId,
            'select',
            [
                'name' => $elementId,
                'class' => 'integration-enable',
                'label' => __('Enable'),
                'title' => __('Enable'),
                'values' => $this->sourceYesNo->toOptionArray(),
                'value' => '0',
            ]
        );

        $subscriptionMode = $fieldset->addField(
            'subscription_mode',
            'select',
            [
                'name' => 'subscription_mode',
                'label' => __('User Subscription Mode'),
                'class' => 'integration-mode',
                'values' => $this->sourceSubscriptionMode->toOptionHash(),
                'note' => $this->getNoteTextSubscriptionMode(),
                'disabled'  => $disabled,
            ]
        );

        $lists = $fieldset->addField(
            'mailchimp_list',
            'text',
            [
                'name' => 'mailchimp_list',
                'label' => __('Contact Lists'),
                'note' => $this->getNoteTextLists(),
                'disabled' => $disabled,
            ]
        );

        $form->getElement('mailchimp_list')
            ->setRenderer($this->getNewInputTableBlock())
            ->getRenderer()
            ->setContainerFieldId('mailchimp_list')
            ->setRowKey('name')
            ->addColumn(
                'enable',
                [
                    'header' => __('Enable'),
                    'index' => 'enable',
                    'type' => 'checkbox',
                    'value' => '1',
                    'width' => '5%',
                    'column_css_class' => 'list-enable-column',
                ]
            )->addColumn(
                'orig_label',
                [
                    'header' => __('Mailchimp List'),
                    'index' => 'orig_label',
                    'type' => 'label',
                    'width' => '40%',
                ]
            )->addColumn(
                'subscribers_count',
                [
                    'header' => __('Subscribers'),
                    'index' => 'subscribers_count',
                    'type' => 'label',
                    'width' => '15%',
                    'align' => 'center',
                ]
            )->addColumn(
                'label',
                [
                    'header' => __('Displayed List Name'),
                    'index' => 'label',
                    'type' => 'input',
                    'width' => '40%',
                ]
            )->addColumn(
                'sort_order',
                [
                    'header' => __('Sort Order'),
                    'index' => 'sort_order',
                    'type' => 'input',
                    'width' => '15%',
                ]
            )->setArray($this->getMailchimpData($popup->getId()));

        $form->setValues($popup->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param $popupId
     * @return array
     */
    private function getMailchimpData($popupId)
    {
        if (! $this->adminhtmlHelper->isMaichimpEnabled()) {
            return [];
        }

        $result = [];
        $collectionData = $this->dataHelper->getPopupMailchimpList($popupId, false);

        foreach ($this->sourceMailchimpList->toOptionHash() as $key => $name) {
            if (array_key_exists($key, $collectionData)) {
                $data = $collectionData[$key]->getData();
            } else {
                $data = [
                    'name' => $key,
                    'label' => $name,
                    'enable' => '0',
                    'sort_order' => 0,
                ];
            }
            $data['subscribers_count'] = $this->sourceMailchimpList->getListSubscribersCount($key);
            $data['orig_label'] = $name;
            $data['id'] = 'mailchimp_list_' . $key;
            $result[$key] = $data;
        }

        $sortCallback = function ($a, $b) {
            return $a['sort_order'] > $b['sort_order'] ? 1 : 0;
        };

        uasort($result, $sortCallback);
        $deletedLists = $this->getDeletedLists($collectionData, $result);
        $result = array_merge($result, $deletedLists);

        return $result;
    }

    /**
     * @param $savedLists
     * @param $alreadyExistingLists
     * @return array
     */
    private function getDeletedLists($savedLists, $alreadyExistingLists)
    {
        $result = [];

        foreach ($savedLists as $listId => $listData) {
            if (! isset($alreadyExistingLists[$listId])) {
                $listData['orig_label'] = sprintf('%s (Deleted)', $listData['label']);
                $listData['subscribers_count'] = '-';
                $result[$listId] = $listData;
            }
        }

        return $result;
    }

    /**
     * @return InputTableBlock
     */
    private function getNewInputTableBlock()
    {
        return $this->getLayout()->createBlock(InputTableBlock::class);
    }

    /**
     * @return ElementDependence
     */
    private function getNewDependencyBlock()
    {
        return $this->getLayout()->createBlock(ElementDependence::class);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    private function getNoteTextSubscriptionMode()
    {
        return __(
            'Here you can allow users to subscribe to the list of their choice or'
            . ' automatically subscribe each new user to all Mailchimp Lists'
        );
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    private function getNoteTextLists()
    {
        return __('Please select Contact Lists where all subscribers should be added to.');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getNoticeText()
    {
        return __(
            'This newsletter subscription will not work until you enable at least one Contact List in the grid below.'
        );
    }
}
