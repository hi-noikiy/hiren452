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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class AffiliateFuture extends AbstractModel
{

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $merchantID = $this->getMerchantId();

        $html = '<script src="//tags.affiliatefuture.com/' . $merchantID . '.js"></script>';

        $prepareHtml = null;

        if ($_section == parent::SECTION_BODYBEGIN) {
            if (isset($_includeon['checkout_success']) && $this->getCpsEnabled()) {
                /**
                 * @var \Magento\Sales\Model\Order $order
                 */
                $order = $this->getLastOrder();
                if ($order && $order->getId()) {

                    $orderRef = $order->getIncrementId();;
                    $orderValue = round($order->getGrandTotal(), 2);
                    $payoutCodes = '';
                    $offlineCode = '';

                    $prepareHtml .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">';
                    $prepareHtml .= '<img src="//scripts.affiliatefuture.com/AFSaleV5.asp?orderID='.$orderRef.'&orderValue='.$orderValue.'&merchant='.$merchantID.'&payoutCodes=&offlineCode=&voucher=&products=&curr=&r=&img=yes" />';
                    $prepareHtml .= '<script language="javascript">
                                (function(){try{
                                    var merchantID = "'.$merchantID.'";
                                    var orderValue = "'.$orderValue.'";
                                    var orderRef = "'.$orderRef.'";
                                    var payoutCodes = "";
                                    var offlineCode = "";
                                    var voucher = "";
                                    var products = "";
                                    var curr = "";

                                    AFProcessSaleV5(merchantID, orderValue, orderRef,payoutCodes,offlineCode, voucher, products, curr);
                                }catch(e){window.console && window.console.log(e)}}());
                            </script>';
                    $prepareHtml .= '</div>';
                }
            }
        } elseif ($_section == parent::SECTION_BODYEND) {
            if (isset($_includeon['registration_success_pages']) && $this->getCplEnabled()) {
                $customer = $this->_customerSession->getCustomer();
                if ($customer && $customer->getId()) {

                    $ref = $customer->getId();
                    $payoutCodes = '';
                    $offlineCode = '';

                    $prepareHtml .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">';
                    $prepareHtml .= '<img src="//scripts.affiliatefuture.com/AFLeadV5.asp?ref='.$ref.'&merchant='.$merchantID.'&payoutCodes=&img=yes" />';
                    $prepareHtml .= '<script language="javascript">
                                (function(){try{
                                    var merchantID = "'.$merchantID.'";
                                    var ref = "'.$ref.'";
                                    var payoutCodes = "";
                                    var offlineCode = "";
                                    var voucher = "";
                                    var products = "";
                                    var curr = "";

                                    AFProcessLeadV5(merchantID, payoutCodes, offlineCode, ref, voucher, products, curr);
                                }catch(e){window.console && window.console.log(e)}}());
                            </script>';
                    $prepareHtml .= '</div>';
                }
            }
        }

        if ($prepareHtml) {
            $html .= '<script language="javascript" src="//scripts.affiliatefuture.com/AFFunctions.js"></script>' . $prepareHtml;
        }

        return $html;
    }
}
