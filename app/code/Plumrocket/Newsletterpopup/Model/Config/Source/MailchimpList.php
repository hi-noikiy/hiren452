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

namespace Plumrocket\Newsletterpopup\Model\Config\Source;

use Plumrocket\Newsletterpopup\Model\Mailchimp\Error;

/**
 * Class MailchimpList
 */
class MailchimpList extends \Plumrocket\Newsletterpopup\Model\Config\Source\Base
{
    /**
     * @var null|array
     */
    private $allLists;

    /**
     * @var array
     */
    private $listSubscribersCount = [];

    /**
     * @return array
     */
    public function toOptionHash()
    {
        if (null === $this->allLists) {
            $this->allLists = [];
            try {
                $model = $this->_adminhtmlHelper->getMcapi();

                if ($model) {
                    $result = $model->lists();
                    $lists = (array)$result['data'];

                    foreach ($lists as $list) {
                        $this->allLists[$list['id']] = $list['name'];
                        $this->listSubscribersCount[$list['id']] = isset($list['stats']['member_count'])
                            ? (int)$list['stats']['member_count']
                            : 0;
                    }
                }
            } catch (Error $e) {
                return [];
            }
        }

        return $this->allLists;
    }

    /**
     * @param $listId
     * @return int
     */
    public function getListSubscribersCount($listId)
    {
        return isset($this->listSubscribersCount[$listId]) ? (int)$this->listSubscribersCount[$listId] : 0;
    }
}
