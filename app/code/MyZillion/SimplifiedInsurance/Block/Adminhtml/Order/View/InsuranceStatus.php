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

namespace MyZillion\SimplifiedInsurance\Block\Adminhtml\Order\View;

use Magento\Sales\Api\Data\OrderItemInterface;
use MyZillion\SimplifiedInsurance\Helper\Data as MyZillionHelper;

/**
 * Block to display Zillion Insurance status on the backend
 */
class InsuranceStatus extends \Magento\Sales\Block\Adminhtml\Order\View
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    private $logger;

    /**
     * @var \MyZillion\SimplifiedInsurance\Helper\ConfigHelper
     */
    private $configHelper;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper
     * @param array $data
     * @return void
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Psr\Log\LoggerInterface $logger,
        \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper,
        array $data = []
    ) {
        $this->serializer = $serializer;
        $this->quoteFactory = $quoteFactory;
        $this->logger = $logger;
        $this->configHelper = $configHelper;
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
    }

    /**
     * Returns the insurance status
     *
     * @param string|integer $websiteId
     * @return string|null
     */
    public function getInsuranceStatus($websiteId = 0)
    {
        if (!$this->configHelper->isEnabled($websiteId)) {
            return null;
        }

        $status = __('N/A');
        try {
            $order = $this->getOrder();
            $quoteId = $this->getOrder()->getQuoteId();
            $quote = $this->quoteFactory->create()->load($quoteId);

            $quoteOfferInsurance = $quote->getData(MyZillionHelper::OFFER_RESPONSE);
            $customerRequestInsurance = (boolean) $quote->getData(MyZillionHelper::CUSTOMER_REQUEST_INSURANCE);
            $orderHaveInsurance = (boolean) $order->getData(MyZillionHelper::CUSTOMER_REQUEST_INSURANCE);
            $orderInsuranceResponse = $order->getData(MyZillionHelper::ORDER_POST_RESPONSE);
            if ($orderInsuranceResponse) {
                $this->serializer->unserialize($orderInsuranceResponse);
            }

            // Does not contain response
            $status = __('Order not insurable');

            if ($quoteOfferInsurance && !$customerRequestInsurance) {
                // Client declined insurance offer
                $status = __('Customer decline the insurance');
            } elseif ($quoteOfferInsurance && $customerRequestInsurance && empty($orderInsuranceResponse)) {
                // Client accepted the insurance offer but the request was not made
                $status = __('Customer request for insurance');
            } elseif ($quoteOfferInsurance && $customerRequestInsurance && !empty($orderInsuranceResponse)) {
                $checkErrors = $this->hasError($orderInsuranceResponse);
                $checkAllSent = $this->checkAllSent($order);
                if (!$checkErrors['has_error'] && $checkAllSent) {
                    // All order products shipped
                    $status = __('Insurance request sent');
                } elseif (!$checkErrors['has_error'] && !$checkAllSent) {
                    // The order is partially shipped
                    $status = __('Partial Order Information Sent, Insurance Requested');
                } else {
                    // Request with status 'error' in shipment
                    $status = 'Order send failed. For the next shipments: ';
                    $status .= implode(', ', $checkErrors['shipments']);
                    $status = __($status);
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $status;
    }

    /**
     * Check if any of the shipments is in error
     *
     * @param string $serializedResponse
     * @return array
     */
    private function hasError($serializedResponse)
    {
        $result = [];
        $result['has_error'] = false;
        $response = $this->serializer->unserialize($serializedResponse);
        if (is_array($response)) {
            foreach ($response as $v) {
                if (isset($v['response']) && isset($v['response']['errors'])) {
                    $result['shipments'][] = $v['shipment_id'];
                    $result['has_error'] = true;
                }
            }
        }

        return $result;
    }

    /**
     * Check the total shipped vs the order
     *
     * @param \Magento\Quote\Model\QuoteFactory $order
     * @return boolean
     */
    private function checkAllSent($order)
    {
        foreach ($order->getAllItems() as $item) {
            if ($item->getQtyToShip() > 0 && !$item->getIsVirtual()
                && !$item->getLockedDoShip() && !$this->itemIsRefunded($item)
            ) {
                // Still have items to ship
                return false;
            }
        }

        return true;
    }

    /**
     * Check if item is refunded.
     *
     * @param OrderItemInterface $item
     * @return boolean
     */
    private function itemIsRefunded(OrderItemInterface $item)
    {
        return $item->getQtyRefunded() == $item->getQtyOrdered();
    }
}
