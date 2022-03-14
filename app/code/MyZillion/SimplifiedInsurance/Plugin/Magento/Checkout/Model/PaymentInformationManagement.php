<?php
/**
 * Copyright (c) 2020, Zillion Insurance Services, Inc.
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package myzillion
 * @subpackage module-simplified-insurance
 * @author Serfe <info@serfe.com>
 * @copyright 2020 Zillion Insurance Services, Inc.
 * @since 1.0.0
 */

namespace MyZillion\SimplifiedInsurance\Plugin\Magento\Checkout\Model;

use MyZillion\SimplifiedInsurance\Helper\Data as MyZillionHelper;

/**
 * Plugin PaymentInformationManagement
 */
class PaymentInformationManagement
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \MyZillion\SimplifiedInsurance\Helper\RequestValidation
     */
    private $requestValidation;

    /**
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    private $request;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepositoryInterface;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepositoryInterface;

    /**
     * @var \MyZillion\SimplifiedInsurance\Helper\ConfigHelper
     */
    private $configHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \MyZillion\SimplifiedInsurance\Helper\RequestValidation $requestValidation
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepositoryInterface
     * @param \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \MyZillion\SimplifiedInsurance\Helper\RequestValidation $requestValidation,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepositoryInterface,
        \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->requestValidation = $requestValidation;
        $this->request = $request;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->quoteRepositoryInterface = $quoteRepositoryInterface;
        $this->configHelper = $configHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * After Save Payment Information And Place Order
     *
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param integer $orderId
     * @return integer $orderId
     */
    public function afterSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        $orderId
    ) {
        try {
            $order = $this->orderRepositoryInterface->get($orderId);
            $storeId = $order->getStoreId();
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
            if (!$this->configHelper->isEnabled($websiteId)) {
                return $orderId;
            }

            $quote = $this->quoteRepositoryInterface->get($order->getQuoteId());
            if ($quote) {
                $requestInsurance = $quote->getData(MyZillionHelper::CUSTOMER_REQUEST_INSURANCE);
                $order->setData(MyZillionHelper::CUSTOMER_REQUEST_INSURANCE, $requestInsurance);
                $order->save();
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $orderId;
    }
}
