<?php

namespace Meetanshi\Partialpro\Model;

use Magento\Framework\App\Helper\Context;
use Magento\Authorizenet\Model\Directpost;
use Magento\Framework\DataObject;
use Meetanshi\Partialpro\Model\Api\Nvp;
use Magento\Paypal\Model\Config;
use Meetanshi\Partialpro\Model\Payment\Braintree;
use Meetanshi\Partialpro\Model\Payment\Sagepay;

class InstallmentPaymentHandler extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $directpost;
    protected $braintree;
    protected $sagepay;
    protected $payment;
    protected $paypalNvp;
    protected $paypalConfig;

    public function __construct(
        Context $context,
        Directpost $directpost,
        DataObject $payment,
        Nvp $paypalNvp,
        Braintree $braintree,
        Sagepay $sagepay,
        Config $paypalConfig
    )
    {
        $this->directpost = $directpost;
        $this->braintree = $braintree;
        $this->sagepay = $sagepay;
        $this->payment = $payment;
        $this->paypalNvp = $paypalNvp;
        $this->paypalConfig = $paypalConfig;
        parent::__construct($context);
    }

    public function payInstallments(\Magento\Sales\Model\Order $order, $payment)
    {
        $this->payment->setAmount($payment['amount']);
        $response = [];

        if ($payment['method'] == 'authorizenet_directpost') {
            $orderId = $order->getIncrementId() . '-' . $payment['installments'] . "-" . date('Y-m-d');

            $this->payment->setIncrementId($orderId);
            $this->payment->setOrder($order);
            if (isset($payment['authorizenet_directpost']['cc_number'])) {
                $this->payment->setCcNumber($payment['authorizenet_directpost']['cc_number']);
            }
            if (isset($payment['authorizenet_directpost']['cc_exp_month'])) {
                $this->payment->setCcExpMonth($payment['authorizenet_directpost']['cc_exp_month']);
            }
            if (isset($payment['authorizenet_directpost']['cc_exp_year'])) {
                $this->payment->setCcExpYear($payment['authorizenet_directpost']['cc_exp_year']);
            }
            if (isset($payment['authorizenet_directpost']['cc_cid'])) {
                $this->payment->setCcCid($payment['authorizenet_directpost']['cc_cid']);
            }
            if (isset($payment['authorizenet_directpost']['cc_type'])) {
                $this->payment->setCcType($payment['authorizenet_directpost']['cc_type']);
            }

            $result = $this->directpost->prepareDirectCallForInstallmentPayment($order, $this->payment);

            if ($result->getXTransId() > 0) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Sorry, Transaction declined by authorizenet.';
                $response['success'] = false;
            }
        } else if ($payment['method'] == 'paypal_express') {
            $orderId = $order->getIncrementId() . '-' . $payment['installments'];

            $this->payment->setIncrementId($orderId);
            $this->paypalConfig->setMethod('paypal_express');
            $response = $this->paypalNvp->callInstallmentSetExpressCheckout($order, $this->payment);

            if ($response['ACK'] == "Success") {
                $redirectUrl = $this->paypalConfig->getExpressCheckoutStartUrl($response['TOKEN']);
                $response['success'] = true;
                $response['redirect_url'] = $redirectUrl;
            } else {
                $response['message'] = 'Sorry, Transaction declined by PayPal.';
                $response['success'] = false;
            }
        } else if ($payment['method'] == 'braintree') {

            $response = $this->braintree->payInstallment($order, $payment);

            if ($response['success']) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Sorry, Transaction declined by Braintree.';
                $response['success'] = false;
            }
        }else if ($payment['method'] == 'sagepay') {

            $response = $this->sagepay->payInstallment($order, $payment);

            if ($response['success']) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Sorry, Transaction declined by Sagepay.';
                $response['success'] = false;
            }
        } else {
            $response['message'] = 'Unable to pay installment. Payment method is not supported.';
            $response['success'] = false;
        }
        return $response;
    }
}