<?php

namespace Meetanshi\Partialpro\Model\Directpost;

use Magento\Sales\Model\Order;
use Magento\Authorizenet\Model\Directpost;

class Request extends Directpost\Request
{
    public function setDataFromOrder(
        Order $order,
        Directpost $paymentMethod
    )
    {
        $payment = $order->getPayment();

        $this->setXType($payment->getAnetTransType());
        $this->setXFpSequence($order->getQuoteId());
        $this->setXInvoiceNum($order->getIncrementId());
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $partialHepler = $om->get('Meetanshi\Partialpro\Helper\Data');
        if ($order->getPartialPayNow() > 0 && $order->getPartialPayLater() > 0 && $partialHepler->isModuleEnabled()) {
            $partialPayNow = $order->getPartialPayNow();
            $basePartialPayNow = $partialHepler->convertCurrency($order->getBaseCurrencyCode(), $order->getOrderCurrencyCode(), $partialPayNow);
            $this->setXAmount($basePartialPayNow);
        } else {
            $this->setXAmount($payment->getBaseAmountAuthorized());
        }
        $this->setXCurrencyCode($order->getBaseCurrencyCode());
        $this->setXTax(
            sprintf('%.2F', $order->getBaseTaxAmount())
        )->setXFreight(
            sprintf('%.2F', $order->getBaseShippingAmount())
        );

        $billing = $order->getBillingAddress();
        if (!empty($billing)) {
            $this->setXFirstName(strval($billing->getFirstname()))
                ->setXLastName(strval($billing->getLastname()))
                ->setXCompany(strval($billing->getCompany()))
                ->setXAddress(strval($billing->getStreetLine(1)))
                ->setXCity(strval($billing->getCity()))
                ->setXState(strval($billing->getRegion()))
                ->setXZip(strval($billing->getPostcode()))
                ->setXCountry(strval($billing->getCountry()))
                ->setXPhone(strval($billing->getTelephone()))
                ->setXFax(strval($billing->getFax()))
                ->setXCustId(strval($billing->getCustomerId()))
                ->setXCustomerIp(strval($order->getRemoteIp()))
                ->setXCustomerTaxId(strval($billing->getTaxId()))
                ->setXEmail(strval($order->getCustomerEmail()))
                ->setXEmailCustomer(strval($paymentMethod->getConfigData('email_customer')))
                ->setXMerchantEmail(strval($paymentMethod->getConfigData('merchant_email')));
        }

        $shipping = $order->getShippingAddress();
        if (!empty($shipping)) {
            $this->setXShipToFirstName(
                strval($shipping->getFirstname())
            )->setXShipToLastName(
                strval($shipping->getLastname())
            )->setXShipToCompany(
                strval($shipping->getCompany())
            )->setXShipToAddress(
                strval($shipping->getStreetLine(1))
            )->setXShipToCity(
                strval($shipping->getCity())
            )->setXShipToState(
                strval($shipping->getRegion())
            )->setXShipToZip(
                strval($shipping->getPostcode())
            )->setXShipToCountry(
                strval($shipping->getCountry())
            );
        }

        $this->setXPoNum(strval($payment->getPoNumber()));

        return $this;
    }
}
