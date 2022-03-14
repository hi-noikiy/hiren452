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

namespace Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList;

use Plumrocket\Newsletterpopup\Model\MailchimpList;
use Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList as ResourceMailchimpList;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(MailchimpList::class, ResourceMailchimpList::class);
    }

    /**
     * @param null $integrationId
     * @param null $popupId
     * @param bool $onlyEnabled
     * @return $this
     */
    public function addIntegrationAndPopupFilter($integrationId = null, $popupId = null, $onlyEnabled = false)
    {
        if ($integrationId) {
            $this->addIntegrationFilter($integrationId);
        }

        if ((int)$popupId > 0) {
            $this->addPopupFilter($popupId);
        }

        if ($onlyEnabled) {
            $this->addOnlyEnabledFilter();
        }

        $this->addDefaultSorting();

        return $this;
    }

    /**
     * @return $this
     */
    public function addDefaultSorting()
    {
        $this->getSelect()->order([
            'integration_id',
            'sort_order',
            'label',
        ]);

        return $this;
    }

    /**
     * @param $popupId
     * @return $this
     */
    public function addPopupFilter($popupId)
    {
        $this->addFieldToFilter('popup_id', (int)$popupId);

        return $this;
    }

    /**
     * @param $integrationId
     * @return $this
     */
    public function addIntegrationFilter($integrationId)
    {
        $condition = is_array($integrationId)
            ? ['in' => $integrationId]
            : (string)$integrationId;

        $this->addFieldToFilter('integration_id', $condition);

        return $this;
    }

    /**
     * @return $this
     */
    public function addOnlyEnabledFilter()
    {
        $this->addFieldToFilter('enable', 1);

        return $this;
    }

    /**
     * @return $this
     */
    public function addSkipMailChimpFilter()
    {
        $this->addFieldToFilter('integration_id', ['neq' => 'mailchimp']);

        return $this;
    }

    /**
     * @return $this
     */
    public function setLoadOnlyListIds()
    {
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS)->columns(['name']);

        return $this;
    }

    /**
     * @return array
     */
    public function getGroupedIntegrationLists()
    {
        $result = [];

        /** @var \Plumrocket\Newsletterpopup\Model\MailchimpList $item */
        foreach ($this->getItems() as $item) {
            $integrationId = $item->getData('integration_id');
            $name = $item->getData('name');
            $result[$integrationId][$name] = $item->getData();
        }

        return $result;
    }
}
