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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Model\ResourceModel;

/**
 * Class MailchimpList
 */
class MailchimpList extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('plumrocket_newsletterpopup_mailchimp_list', 'entity_id');
    }

    /**
     * @param array $data
     * @return int
     */
    public function insertAndUpdateLists(array $data)
    {
        $updateColumns = [
            'popup_id',
            'integration_id',
            'name',
            'label',
            'enable',
            'sort_order',
        ];

        return $this->_getConnection('write')
            ->insertOnDuplicate(
                $this->getMainTable(),
                $data,
                $updateColumns
            );
    }
}
