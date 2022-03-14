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

use Plumrocket\Newsletterpopup\Helper\Data as DataHelper;
use Plumrocket\Newsletterpopup\Block\Adminhtml\Popups\Edit\Renderer\Label as ExtendedLabel;

/**
 * Class Inactive
 */
class Inactive extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('popup_');

        /* Add new fieldset */
        $fieldset = $form->addFieldset('inactive_fieldset', ['legend' => __('Integrations')]);

        /* Add invisible notice label */
        $fieldset->addType('extended_label', ExtendedLabel::class);
        $fieldName = 'inactive_extended_label';
        $fieldset->addField($fieldName, 'extended_label', [
            'hidden' => false,
        ])->setValue($this->getNoticeText());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getNoticeText()
    {
        $href = $this->getUrl('adminhtml/system_config/edit', [
            'section' => DataHelper::SECTION_ID,
        ]);

        return __(
            'Integrations are not enabled in the "System Configuration -> Plumrocket Newsletter Popup".'
            . ' Please <a target="_blank" href="%1">click here</a> to enable at least one integration.',
            $href
        );
    }
}
