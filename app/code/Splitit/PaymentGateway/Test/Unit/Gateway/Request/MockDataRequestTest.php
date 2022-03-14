<?php

namespace Splitit\PaymentGateway\Test\Unit\Gateway\Request;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment;
use Splitit\PaymentGateway\Gateway\Http\Client\SplititCreateApiImplementation;
use Splitit\PaymentGateway\Gateway\Request\MockDataRequest;

class MockDataRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int $forceResultCode
     * @param int|null $transactionResult
     *
     * @dataProvider transactionResultsDataProvider
     */
    public function testBuild($forceResultCode, $transactionResult)
    {
        $expectation = [
            MockDataRequest::FORCE_RESULT => $forceResultCode
        ];

        $paymentDO = $this->getMock(PaymentDataObjectInterface::class);
        $paymentModel = $this->getMock(InfoInterface::class);


        $paymentDO->expects(static::once())
            ->method('getPayment')
            ->willReturn($paymentModel);

        $paymentModel->expects(static::once())
            ->method('getAdditionalInformation')
            ->with('transaction_result')
            ->willReturn(
                $transactionResult
            );

        $request = new MockDataRequest();

        static::assertEquals(
            $expectation,
            $request->build(['payment' => $paymentDO])
        );
    }

    /**
     * @return array
     */
    public function transactionResultsDataProvider()
    {
        return [
            [
                'forceResultCode' => SplititCreateApiImplementation::SUCCESS,
                'transactionResult' => null
            ],
            [
                'forceResultCode' => SplititCreateApiImplementation::SUCCESS,
                'transactionResult' => SplititCreateApiImplementation::SUCCESS
            ],
            [
                'forceResultCode' => SplititCreateApiImplementation::FAILURE,
                'transactionResult' => SplititCreateApiImplementation::FAILURE
            ]
        ];
    }
}
