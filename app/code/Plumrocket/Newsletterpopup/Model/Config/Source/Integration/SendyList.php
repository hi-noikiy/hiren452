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

namespace Plumrocket\Newsletterpopup\Model\Config\Source\Integration;

class SendyList extends \Magento\Framework\App\Config\Value
{
    /**
     * Prepare data before save
     *
     * @return \Magento\Framework\App\Config\Value
     */
    public function beforeSave()
    {
        /** @var array $value */
        $value = $this->getValue();

        unset($value['__empty']);
        $this->setValue(json_encode($value));

        return parent::beforeSave();
    }

    /**
     * Prepare data after load
     *
     * @return \Magento\Framework\App\Config\Value
     */
    protected function _afterLoad()
    {
        /** @var string $value */
        $value = $this->getValue();

        if (! empty($value)) {
            $this->setValue(json_decode($value, true));
        }

        return parent::_afterLoad();
    }
}