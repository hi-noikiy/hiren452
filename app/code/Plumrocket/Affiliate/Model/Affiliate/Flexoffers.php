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
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class Flexoffers extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    public function getAdvertiserId()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['advertiser_id']) ? $additionalData['advertiser_id'] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = '';

        if ($_section == parent::SECTION_BODYEND) {
            $order = $this->getLastOrder();
            if ($order && $order->getId()) {

                $amount = round($order->getGrandTotal() - $order->getShippingAmount() - $order->getTaxAmount(), 2);
                $src = 'https://track.flexlinks.com/tracker.aspx?AID='.$this->getAdvertiserId()
                    . "&AMT=" .$amount
                    . "&UID=" . $order->getIncrementId();

                $html = '
                    <!-- BEGIN OF FLEXOFFERS.COM TRACKING CODE -->
                    <img src="'.$src.'" width="1" height="1" alt="" />
                    <!-- END OF FLEXOFFERS.COM TRACKING CODE -->
                ';

            }
        }
        return $html;
    }
}
