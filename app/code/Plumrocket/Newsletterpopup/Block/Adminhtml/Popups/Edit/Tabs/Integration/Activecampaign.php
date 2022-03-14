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

/**
 * Class Activecampaign
 */
class Activecampaign extends \Plumrocket\Newsletterpopup\Block\Adminhtml\Popups\Edit\Tabs\AbstractIntegration
{
    /**
     * @return string
     */
    public function getIntegrationId()
    {
        return \Plumrocket\Newsletterpopup\Model\Integration\ActiveCampaign::INTEGRATION_ID;
    }

    /**
     * @return string
     */
    public function getIntegrationTitle()
    {
        return __('ActiveCampaign');
    }

    /**
     * @param \Magento\Framework\Data\Form $form
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function addFieldset(\Magento\Framework\Data\Form $form)
    {
        if (! empty($this->getAllLists())) {
            return parent::addFieldset($form);
        }

        $fieldset = $form->addFieldset(
            $this->getIntegrationId() . '_fieldset',
            [
                'legend' => $this->getIntegrationTitle()
            ]
        );

        $this->addFieldNotice($fieldset, $this->getNoticeText(), true);

        return $fieldset;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getNoticeText()
    {
        $emptyListsNoticeText = __(
            'ActiveCampaign newsletter subscription will not work until you create at least one Contact List '
            . 'at activecampaign.com.'
        );

        return $this->isEnabled() && empty($this->getAllLists())
            ? $emptyListsNoticeText
            : parent::getNoticeText();
    }
}
