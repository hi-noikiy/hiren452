<?php

namespace Meetanshi\Partialpro\Model;

class Directpost extends \Magento\Authorizenet\Model\Directpost
{
    public function prepareDirectCallForInstallmentPayment(\Magento\Sales\Model\Order $order, $payment)
    {
        if ($this->getConfigData('payment_action') == 'authorize') {
            $payment->setAnetTransType(self::REQUEST_TYPE_AUTH_ONLY);
        } else {
            $payment->setAnetTransType(self::REQUEST_TYPE_AUTH_CAPTURE);
        }

        $request = $this->buildRequest($payment);

        $result = $this->postInstallmentRequest($request);
        return $result;
    }

    protected function postInstallmentRequest(\Magento\Authorizenet\Model\Request $request)
    {
        $result = $this->responseFactory->create();
        $client = $this->httpClientFactory->create();
        $url = $this->getConfigData('cgi_url') ?: self::CGI_URL;
        $debugData = ['url' => $url, 'request' => $request->getData()];
        $client->setUri($url);
        $client->setConfig(['maxredirects' => 0, 'timeout' => 30]);

        foreach ($request->getData() as $key => $value) {
            $request->setData($key, str_replace(self::RESPONSE_DELIM_CHAR, '', $value));
        }

        $request->setXDelimChar(self::RESPONSE_DELIM_CHAR);
        $client->setParameterPost($request->getData());
        $client->setMethod(\Zend_Http_Client::POST);
        try {
            $response = $client->request();
            $responseBody = $response->getBody();
            $r = explode(self::RESPONSE_DELIM_CHAR, $responseBody);
        } catch (\Exception $e) {
            $result->setXResponseCode(-1)
                ->setXResponseReasonCode($e->getCode())
                ->setXResponseReasonText($e->getMessage());

            throw new \Magento\Framework\Exception\LocalizedException(
                $this->dataHelper->wrapGatewayError($e->getMessage())
            );
        } finally {
            $this->_debug($debugData);
        }

        $r = explode(self::RESPONSE_DELIM_CHAR, $responseBody);
        if ($r) {
            $result->setXResponseCode((int)str_replace('"', '', $r[0]))
                ->setXResponseReasonCode((int)str_replace('"', '', $r[2]))
                ->setXResponseReasonText($r[3])
                ->setXAvsCode($r[5])
                ->setXTransId($r[6])
                ->setXInvoiceNum($r[7])
                ->setXAmount($r[9])
                ->setXMethod($r[10])
                ->setXType($r[11])
                ->setData('x_MD5_Hash', $r[37])
                ->setXAccountNumber($r[50]);
            $this->_debug($result->getData());
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong in the payment gateway.')
            );
        }
        return $result;
    }

    protected function matchAmount($amount)
    {
        $response = $this->getResponse();
        $orderIncrementId = $response->getXInvoiceNum();
        if ($orderIncrementId) {
            $order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);
        }

        return ((sprintf('%.2F', $amount) == sprintf('%.2F', $this->getResponse()->getXAmount())) || (sprintf('%.2F', $order->getBasePaidAmount()) == sprintf('%.2F', $this->getResponse()->getXAmount())));
    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if ($amount <= 0) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid amount for capture.'));
        }
        $order = $payment->getOrder();
        if ($order->getPaidAmount()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $installmentModel = $objectManager->create('Meetanshi\Partialpro\Model\Installments');
            $installments = $installmentModel->getInstallmentsByOrderId($order->getId());
            $amount = $installments->getFirstItem()->getInstallmentAmount();
        }
        $payment->setAmount($amount);

        if ($payment->getParentTransactionId()) {
            $payment->setAnetTransType(self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE);
            $payment->setXTransId($this->getRealParentTransactionId($payment));
        } else {
            $payment->setAnetTransType(self::REQUEST_TYPE_AUTH_CAPTURE);
        }

        $result = $this->getResponse();
        if (empty($result->getData())) {
            $request = $this->buildRequest($payment);
            $result = $this->postRequest($request);
        }

        return $this->processCapture($result, $payment);
    }

    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $last4 = $payment->getCcLast4();
        $payment->setCcLast4($payment->decrypt($last4));
        try {
            $order = $payment->getOrder();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $partialHepler = $objectManager->get('Meetanshi\Partialpro\Helper\Data');
            if ($order->getPartialPayNow() && $partialHepler->isModuleEnabled()) {

                $installmentModel = $objectManager->create('Meetanshi\Partialpro\Model\Installments');
                $installments = $installmentModel->getInstallmentsByOrderId($order->getId());
                $amount = $installments->getFirstItem()->getInstallmentAmount();
                $amount = $partialHepler->convertCurrency($order->getBaseCurrencyCode(), $order->getOrderCurrencyCode(), $amount);

            }
            $this->processRefund($payment, $amount);
        } catch (\Exception $e) {
            $payment->setCcLast4($last4);
            throw $e;
        }
        $payment->setCcLast4($last4);
        return $this;
    }
}
