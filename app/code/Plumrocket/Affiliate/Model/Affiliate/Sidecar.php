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

class Sidecar extends AbstractModel
{
    const SITE_ID_PARAM = 'PlumrocketAffiliateLinkshareSiteId';

    /**
     * {@inheritdoc}
     */
    public function getSitename()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['sitename']) ? $additionalData['sitename'] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        if ($_section != parent::SECTION_BODYEND) {
            return;
        }

        $html = '';

        if (isset($_includeon['checkout_success'])) { //checkout success page
            $order = $this->getLastOrder();
            if ($order && $order->getId()) {

                $postCode = '';
                if ($shippingAdderss = $order->getShippingAddress()) {
                    $postCode = $shippingAdderss->getPostcode();
                }

                $items = [];
                foreach ($order->getAllVisibleItems() as $item) {
                    $items[] = [
                        'product_id' => $item->getSku(),
                        'unit_price' => round($item->getBasePrice(), 2),
                        'quantity' => round($item->getQtyOrdered()),
                    ];
                }

                $discount = round(abs($order->getDiscountAmount()), 2);
                $transactions = [
                    'add' => true,
                    'data' => [
                        'order_id' => $order->getIncrementId(),
                        'subtotal' => round($order->getSubtotal(), 2),
                        'tax' => round($order->getTaxAmount(), 2),
                        'shipping' => round($order->getShippingAmount(), 2),
                        'discounts' => $discount,
                        'total' => round($order->getGrandTotal(), 2),
                        'zipcode' => $postCode,
                    ],
                ];

                $transactions['items'] = $items;

                $discounts = [];
                if ($discount) {
                    $discounts = [
                        'name' => $order->getDiscountDescription(),
                        'amount' => $discount,
                    ];
                    $transactions['discounts'] = $discounts;
                }

                $html .= '
                    <script type="text/javascript">
                        var sidecar = sidecar || {};
                        sidecar.transactions = '.json_encode($transactions).';
                    </script>';

            }
        } elseif (isset($_includeon['product_page'])) { //product page
            if ($product = $this->_registry->registry('current_product')) {
                $html .= '
                    <script type="text/javascript">
                            var sidecar = sidecar || {};
                            sidecar.product_info = {
                                product_id: "'.$product->getSku().'"
                            };
                    </script>';
            }
        }

        $html .= '<script src="https://d3v27wwd40f0xu.cloudfront.net/js/tracking/sidecar_'.$this->getSitename().'.js" type="text/javascript"></script>';

        return $html;
    }
}
