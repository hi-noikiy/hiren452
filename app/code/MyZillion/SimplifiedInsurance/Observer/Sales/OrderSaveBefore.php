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

namespace MyZillion\SimplifiedInsurance\Observer\Sales;

use Magento\Framework\Event\ObserverInterface;
use MyZillion\SimplifiedInsurance\Helper\Data as MyZillionHelper;

/**
 * Observer for sales_order_save_before
 * Update order.cusomter_request_insurance value from quote value
 *
 * @author Exequiel Lares <exequiel@serfe.com>
 */
class OrderSaveBefore implements ObserverInterface
{
    /**
     * @var string
     */
    protected $eventName = 'sales_order_save_before';

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \MyZillion\SimplifiedInsurance\Helper\ConfigHelper
     */
    protected $configHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface         $quoteRepository
     * @param \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->configHelper = $configHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try{
            $order = $observer->getEvent()->getOrder();
            $storeId = $order->getStoreId();
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
            if (!$this->configHelper->isEnabled($websiteId)) {
                return $this;
            }

            $quote = $this->quoteRepository->get($order->getQuoteId());

            if ($quote) {
                $quoteValue = $quote->getData(MyZillionHelper::CUSTOMER_REQUEST_INSURANCE);
                $orderValue = $order->getData(MyZillionHelper::CUSTOMER_REQUEST_INSURANCE);
                if ($quoteValue !== $orderValue) {
                    $order->setData(MyZillionHelper::CUSTOMER_REQUEST_INSURANCE, $quoteValue);
                }
            }
        } catch (\Exception $e) {
            // For cases where the quote was removed
            // Exception = {code => 0, messange => 'No such entity with cartId = 999999}'
            // Example via Magento quote cleaner cron job
            $checkMessage = strpos($e->getMessage(),'No such entity with cartId');
            if($checkMessage !==false){
                return $this;
            }

            $this->logger->critical($e->getMessage());
        }

        return $this;
    }
}
