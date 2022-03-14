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

class EbayEnterprise extends AbstractModel
{
    /**
     * Get Pj Program Id
     * @return string
     */
    public function getPjProgramId()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['pj_program_id']) ? $additionalData['pj_program_id'] : '';
    }

    /**
     * Get Pj Cps Enables
     * @return string
     */
    public function getPjCpsEnabled()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['pj_cps_enable']) ? $additionalData['pj_cps_enable'] : '';
    }

    /**
     * Get Pj Cpl Enabled
     * @return string
     */
    public function getPjCplEnabled()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['pj_cpl_enable']) ? $additionalData['pj_cpl_enable'] : '';
    }

    /**
     * Get Fb Site Id
     * @return string
     */
    public function getFbSiteId()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['fb_site_id']) ? $additionalData['fb_site_id'] : '';
    }

    /**
     * Get Fb Cps enabled
     * @return string
     */
    public function getFbCpsEnabled()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['fb_cps_enable']) ? $additionalData['fb_cps_enable'] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = '';
        $scheme = $this->_request->getScheme();

        if ($_section == parent::SECTION_BODYBEGIN) {

            // PepperJam.

            $pjParams = [];
            switch (true) {
                case $this->getPjCplEnabled() && isset($_includeon['registration_success_pages']):
                    $currentCustommerId = null;
                    if ($currentCustommer = $this->_customerSession->getCustomer()) {
                        $currentCustommerId = $currentCustommer->getId();
                    }

                    $pjParams = [
                        'TYPE=2',
                        'CID='. $currentCustommerId, // not find in documentation
                    ];
                    break;
                case $this->getPjCpsEnabled() && isset($_includeon['checkout_success']):
                    $order = $this->getLastOrder();
                    if ($order && $order->getId()) {
                        $products = [
                            'ITEM' => [],
                            'QTY' => [],
                            'AMOUNT' => [],
                        ];
                        $n = 1;
                        foreach ($order->getAllVisibleItems() as $item) {
                            $products['ITEM'][$n]   = $item->getSku();
                            $products['QTY'][$n]    = round($item->getQtyOrdered());
                            $products['AMOUNT'][$n] = round($item->getPrice(), 2);
                            $n++;
                        }

                        $pjParams = [
                            'INT=ITEMIZED',
                            http_build_query($products['ITEM'], 'ITEM'),
                            http_build_query($products['QTY'], 'QTY'),
                            http_build_query($products['AMOUNT'], 'AMOUNT'),
                            'OID='. urlencode($order->getIncrementId()),
                        ];

                        if ($couponCode = $order->getCouponCode()) {
                            $pjParams[] = 'PROMOCODE='. urlencode($couponCode);
                        }

                    }
                    break;
            }

            if ($pjParams) {
                $html .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                                <iframe src="https://t.pepperjamnetwork.com/track?PID='. (int)$this->getPjProgramId() . '&'. implode('&', $pjParams) .'" width="1" height="1" frameborder="0"></iframe>
                            </div>';
            }

        } elseif ($_section == parent::SECTION_BODYEND) {

            // Fetchback.
            if (!$this->getFbCpsEnabled()) {
                return $html;
            }

            $fbParams = [];
            switch (true) {
                case isset($_includeon['checkout_success']):
                    $order = $this->getLastOrder();
                    if ($order && $order->getId()) {

                        $totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);

                        $products = [];
                        foreach ($order->getAllVisibleItems() as $item) {
                            $products[] = $item->getSku();
                        }

                        $fbParams = array_merge(
                            $this->_getFbBaseParams(),
                            [
                                'name'              => 'success',
                                'oid'               => $order->getIncrementId(),
                                'purchase_products' => implode(',', $products),
                                'crv'               => $totalAmount,
                            ]
                        );
                    }
                    break;
                case isset($_includeon['cart_page']):
                    $quote = $this->_checkoutSession->getQuote();
                    $products = [];
                    foreach ($quote->getAllVisibleItems() as $item) {
                        $products[] = $item->getSku();
                    }
                    $fbParams = array_merge(
                        $this->_getFbBaseParams(),
                        [
                            'abandon_products' => implode(',', $products)
                        ]
                    );
                    break;
                case isset($_includeon['product_page']):
                    $product = $this->_registry->registry('current_product');
                    $fbParams = array_merge(
                        $this->_getFbBaseParams(),
                        [
                            'browse_products' => $product->getSku()
                        ]
                    );
                    break;
                default:
                    $fbParams = $this->_getFbBaseParams();
                    break;
            }

            if ($fbParams) {
                $html .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                                <iframe src="'. $scheme .'://pixel.fetchback.com/serve/fb/pdj?'. http_build_query($fbParams) .'" scrolling="no" width="1" height="1" marginheight="0" marginwidth="0" frameborder="0"></iframe>
                            </div>';
            }

        }

        return $html;
    }

    /**
     * Get Fb Base Params
     * @return Array
     */
    protected function _getFbBaseParams()
    {
        return [
            'cat'   => '',
            'name'  => 'landing',
            'sid'   => $this->getFbSiteId(),
        ];
    }
}
