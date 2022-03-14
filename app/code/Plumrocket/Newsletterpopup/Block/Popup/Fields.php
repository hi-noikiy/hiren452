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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Block\Popup;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Block\Widget\AbstractWidget;
use Magento\Customer\Helper\Address;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\Newsletterpopup\Block\Popup\Fields\DataPrivacyConsents;
use Plumrocket\Newsletterpopup\Block\Popup\Fields\Field;
use Plumrocket\Newsletterpopup\Model\Popup\GetFields as GetPopupFields;

class Fields extends AbstractWidget
{
    /**
     * @var GetPopupFields
     */
    private $getPopupFields;

    /**
     * Fields constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Customer\Helper\Address                  $addressHelper
     * @param \Magento\Customer\Api\CustomerMetadataInterface   $customerMetadata
     * @param \Plumrocket\Newsletterpopup\Model\Popup\GetFields $getPopupFields
     * @param array                                             $data
     */
    public function __construct(
        Context $context,
        Address $addressHelper,
        CustomerMetadataInterface $customerMetadata,
        GetPopupFields $getPopupFields,
        array $data = []
    ) {
        $this->setTemplate('popup/fields.phtml');

        parent::__construct($context, $addressHelper, $customerMetadata, $data);
        $this->getPopupFields = $getPopupFields;
    }

    /**
     * @return \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface[]
     */
    public function getFields(): array
    {
        if ($data = $this->_getPopup()->getData('custom_signup_fields')) {
            return $data;
        }
        $popupId = (int) $this->_getPopup()->getId();
        if ($this->_getPopup()->getIsTemplate()) {
            $popupId = 0;
        }
        return $this->getPopupFields->execute($popupId);
    }

    /**
     * @param \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface $field
     * @return Field|\Magento\Customer\Block\Widget\AbstractWidget
     */
    public function createBlock($field)
    {
        $blockName = 'field';
        if (in_array($field->getName(), ['dob', 'gender', 'prefix', 'suffix', 'agreement'])) {
            $blockName = $field->getName();
        }

        if ('data_privacy_consents' === $field->getName()) {
            $fullBlockName = DataPrivacyConsents::class;
        } else {
            $fullBlockName = 'Plumrocket\Newsletterpopup\Block\Popup\Fields\\' . ucfirst($blockName);
        }

        /** @var Field|AbstractWidget $fieldBlock */
        $fieldBlock = $this->getLayout()->createBlock($fullBlockName);

        return $fieldBlock->setField($field)
            ->setPopup($this->_getPopup());
    }

    /**
     * @param \Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface $field
     * @return string
     */
    public function getFieldHtml($field)
    {
        $fieldBlock = $this->createBlock($field);
        return $fieldBlock ? $fieldBlock->toHtml() : '';
    }

    protected function _getPopup()
    {
        return $this->getParentBlock()->getPopup();
    }
}
