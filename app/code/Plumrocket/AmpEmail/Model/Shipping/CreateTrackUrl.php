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
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\AmpEmail\Model\Shipping;

/**
 * Class CreateTrackUrl
 *
 * @api
 * @since 1.0.1
 */
class CreateTrackUrl
{
    /**
     * @param \Magento\Sales\Api\Data\TrackInterface $track
     * @return string
     */
    public function execute(\Magento\Sales\Api\Data\TrackInterface $track) : string
    {
        $trackingNumber = $track->getTrackNumber();

        switch ($track->getCarrierCode()) {
            case 'fedex':
                $url = "https://www.fedex.com/apps/fedextrack/?action=track&trackingnumber=$trackingNumber";
                break;
            case 'dhl':
                $url = "https://www.dhl.com/en/express/tracking.shtml?AWB=$trackingNumber&brand=DHL";
                break;
            case 'ups':
                $url = "https://www.ups.com/track?tracknum=$trackingNumber/";
                break;
            case 'usps':
                $url = "https://tools.usps.com/go/TrackConfirmAction?tLabels=$trackingNumber";
                break;
            default:
                $url = '';
        }

        return $url;
    }
}
