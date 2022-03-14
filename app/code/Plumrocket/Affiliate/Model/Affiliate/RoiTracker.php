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
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class RoiTracker extends AbstractModel
{
    public function getMerchantId()
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData['merchant_id']) ? $additionalData['merchant_id'] : '';
    }

    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = null;

        if ($_section == parent::SECTION_BODYEND) {
            $order = $this->getLastOrder();
            if ($order && $order->getId()) {
                $totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);

                $html .= '<script type="text/javascript">
                                var _roi = _roi || [];

                                // Step 1: add base order details

                                _roi.push(["_setMerchantId", "' . $this->getMerchantId() . '"]); // required
                                _roi.push(["_setOrderId", "' . $order->getIncrementId() . '"]); // unique customer order ID
                                _roi.push(["_setOrderAmount", "' . $totalAmount . '"]); // order total without tax and shipping
                                _roi.push(["_setOrderNotes", ""]); // notes on order, up to 50 characters

                                // Step 2: add every item in the order
                                // where your e-commerce engine loops through each item in the cart and prints out _addItem for each
                                // please note that the order of the values must be followed to ensure reporting accuracy';

                foreach ($order->getAllVisibleItems() as $item) {
                    $product = $item->getProduct();

                    $categoryIds = $product->getCategoryIds();
                    $firstCategoryName = '';
                    if (count($categoryIds)) {
                        $firstCategoryName = $this->_categoryFactory->create()->load($categoryIds[0])->getName();
                    }

                    $html .= '
                                _roi.push(["_addItem",
                                "' . $product->getSku() . '", // Merchant sku
                                "' . $product->getName() . '", // Product name
                                "' . $categoryIds[0] . '", // Category id
                                "' . $firstCategoryName . '", // Category name
                                "' . $item->getPrice() . '", // Unit price
                                "' . (int)$item->getQtyOrdered() . '" // Item quantity
                                ]);';
                }

                $html .= '      // Step 3: submit transaction to ECN ROI tracker
                                _roi.push(["_trackTrans"]);
                                </script>
                                <script type="text/javascript" src="https://stat.dealtime.com/ROI/ROI2.js">
                        </script>';

            }
        }

        return $html;
    }
}
