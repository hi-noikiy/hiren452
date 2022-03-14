<?php

namespace Meetanshi\Partialpro\Gateway\Request;

use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Meetanshi\Partialpro\Helper\Mtn as MtnHelper;
use Magento\Checkout\Model\Session as CheckoutSession;

class MtnCardDetailsDataBuilder implements BuilderInterface
{

    /**
     *
     */
    const PAYMENT_METHOD = 'paymentMethod';

    /**
     * @var MtnHelper
     */
    private $mtnHelper;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * MtnCardDetailsDataBuilder constructor.
     * @param MtnHelper $mtnHelper
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(MtnHelper $mtnHelper, CheckoutSession $checkoutSession)
    {
        $this->mtnHelper = $mtnHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();

        $quote = $this->checkoutSession->getQuote();
        $amount = $quote->getPartialPayNow();
        if ($amount <= 0) {
            $amount = SubjectReader::readAmount($buildSubject);
        }
        $data = $payment->getAdditionalInformation();

        $code = $this->mtnHelper->getBillMapCode();
        $paassword = $this->mtnHelper->getBillMapPass();
        $msisdn = $data['mtn_number'];
        $refrence = $order->getOrderIncrementId();
        $metadata = $order->getOrderIncrementId();

        ContextHelper::assertOrderPayment($payment);

        $tranParams = "Code=$code&Password=$paassword&MSISDN=$msisdn&Reference=$refrence&Amount=$amount&MetaData=$metadata";

        return [
            self::PAYMENT_METHOD => $tranParams
        ];
    }
}
