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

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Plumrocket\Newsletterpopup\Api\Data\PopupFieldDataInterface;

class FormField extends AbstractDb
{
    const MAIN_TABLE_NAME = 'plumrocket_newsletterpopup_form_fields';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, 'entity_id');
    }

    /**
     * @param int $popupId
     * @return string[]
     */
    public function getAllFieldsNames(int $popupId = 0): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable(self::MAIN_TABLE_NAME),
            PopupFieldDataInterface::NAME
        );

        $select->where(PopupFieldDataInterface::POPUP_ID . ' = ?', $popupId);

        return $connection->fetchCol($select);
    }
}
