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
 * @package     Plumrocket_ShippingTracking
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ShippingTracking\Block\Adminhtml\System\Config\Connection;

use Plumrocket\ShippingTracking\Helper\Config;

class Fedex extends \Plumrocket\ShippingTracking\Block\Adminhtml\System\Config\Connection\AbstractButton
{
    /**
     * Service prefix
     */
    const SERVICE_PREFIX = 'prshippingtracking_services_fedex_api_';

    /**
     * @param null $htmlId
     * @return string
     */
    public function getOnclick($htmlId = null)
    {
        return sprintf(
            'window.prTrackingTestConnection(\'%s\', \'%s\', \'%s\'); return false;',
            $this->getUrl(Config::TEST_CONNECTION_URL, ['carrier' => Config::FEDEX]),
            $htmlId,
            $this->getFieldIds()
        );
    }

    /**
     * @return string
     */
    public function getFieldIds()
    {
        $suffixIds = [
            'key',
            'password',
            'account_number',
            'meter_number',
            'sandbox_mode'
        ];
        $ids = implode("," . self::SERVICE_PREFIX, $suffixIds);

        return self::SERVICE_PREFIX . $ids;
    }
}