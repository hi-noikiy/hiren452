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
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Framework\Exception\LocalizedException;
use MyZillion\SimplifiedInsurance\Helper\Data as MyZillionHelper;
use MyZillion\SimplifiedInsurance\Model\ShipmentPostRequest as ShipmentPostRequest;

/**
 * Description of OrderShipmentSaveAfter
 */
class OrderShipmentSaveAfter implements ObserverInterface
{
    const SUCCESS_RESPONSE = 'Order created';
    const RESPONSE_SHIPMENT_ID_FIELD = 'shipment_id';
    const RESPONSE_ERROR_FIELD = 'error';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \MyZillion\SimplifiedInsurance\Api\AdapterInterface
     */
    protected $insuranceAdapter;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \MyZillion\SimplifiedInsurance\Helper\ConfigHelper
     */
    protected $configHelper;

    /**
     * @var \MyZillion\SimplifiedInsurance\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $datem2;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var ShipmentPostRequest
     */
    private $shipmentPostRequest;

    /**
     * @var string
     */
    protected $eventName = 'sales_order_shipment_save_after';

    /**
     * @var \MyZillion\SimplifiedInsurance\Api\MapperInterface
     */
    private $dataMapper;

    /**
     * Constructor
     *
     * @param \MyZillion\SimplifiedInsurance\Api\AdapterInterface $insuranceAdapter
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper
     * @param \MyZillion\SimplifiedInsurance\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datem2
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param ShipmentPostRequest $shipmentPostRequest
     * @param \MyZillion\SimplifiedInsurance\Api\MapperInterface $dataMapper
     */
    public function __construct(
        \MyZillion\SimplifiedInsurance\Api\AdapterInterface $insuranceAdapter,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper,
        \MyZillion\SimplifiedInsurance\Helper\Data $helper,
        \Magento\Framework\Stdlib\DateTime\DateTime $datem2,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ShipmentPostRequest $shipmentPostRequest,
        \MyZillion\SimplifiedInsurance\Api\MapperInterface $dataMapper
    ) {
        $this->insuranceAdapter = $insuranceAdapter;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
        $this->serializer = $serializer;
        $this->configHelper = $configHelper;
        $this->helper = $helper;
        $this->datem2 = $datem2;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->shipmentPostRequest = $shipmentPostRequest;
        $this->dataMapper = $dataMapper;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $storeId = $shipment->getStoreId();
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

        try {
            // Checking if order should be sent
            $postOrderData = $this->dataMapper->shipmentToPostOrderRequest($shipment);
            $offerFieldName = $this->configHelper->getZillionOfferTypeFieldName($websiteId);
            $sendData = isset($postOrderData['order'][$offerFieldName]) ? $postOrderData['order'][$offerFieldName] : false;
            $isEnabled = $this->configHelper->isEnabled($websiteId);

            // Data should be sent "always if enabled" OR "if disabled but offer accepted"
            if(($isEnabled) || (!$isEnabled && $sendData)){
                // We check if the shipment was sent to Zillion to avoid duplicates.
                $order = $shipment->getOrder();
                $data = json_decode($order->getData(MyZillionHelper::ORDER_POST_RESPONSE), true);
                if (!empty($data)) {
                    foreach ($data as $k => $v) {
                        if ($v[self::RESPONSE_SHIPMENT_ID_FIELD] == $shipment->getId()
                            && !(filter_var($v[self::RESPONSE_ERROR_FIELD], FILTER_VALIDATE_BOOLEAN))
                        ) {
                            // Breaking to the return if the offer
                            return $this;
                        }
                    }
                    unset($k, $v);
                }

                // We don't validate if module is enabled at this point.
                // If the related order has Zillion information, then the request is sent because the order
                // was placed before the module was disabled. This allow to process all of the orders that have an offer
                // Initialize adapter with proper configuration
                $this->insuranceAdapter->initialize($websiteId);

                $this->requestOrderPost($shipment);
            }
        } catch (LocalizedException $e) {
            $this->logger->info($e->getMessage());
        } catch (\Exception $e) {
            // In case of any other error that can exists
            $this->logger->warn($e->getMessage());
        }

        return $this;
    }

    /**
     * Validate if quote offer response is valid
     *
     * @param  integer|string $quoteId
     * @return boolean
     */
    public function quoteOfferIsValid($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $storeId = $quote->getStoreId();
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        // Initialize adapter with proper configuration
        $this->insuranceAdapter->initialize($websiteId);
        $offerResponse = $quote->getData(self::OFFER_RESPONSE);
        if ($offerResponse) {
            $offerResponse = $this->serializer->unserialize($offerResponse);
            $insurance = $this->insuranceAdapter->parseOfferResponse($offerResponse);
            if ($insurance > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Submit policy request to the API
     *
     * @param ShipmentInterface $shipment
     * @return ShipmentInterface
     */
    protected function requestOrderPost($shipment)
    {
        try {
            $order = $shipment->getOrder();
            $orderPostResponse = $this->insuranceAdapter->sendPostOrderRequest($shipment);
            $policyResponse = $this->armedResponseForShipping($orderPostResponse, $shipment->getId());

            $data = $order->getData(MyZillionHelper::ORDER_POST_RESPONSE);

            if (empty($data)) {
                $data = [];
            } else {
                $data = $this->serializer->unserialize($data);
            }

            array_push($data, $policyResponse);
            $jsonResponse = $this->serializer->serialize($data);
            $order->setData(MyZillionHelper::ORDER_POST_RESPONSE, $jsonResponse);
            $order->save();

            // Validate Status response
            $statusResponse = $this->validateStatusOrderPostResponse($orderPostResponse);
            // save status shipment post order
            $this->saveStatusShipment($order->getId(), $shipment->getId(), $statusResponse);
            if ($statusResponse === 'success') {
                // Success message
                $msg = 'The shipment information was successfully sent to Zillion.';
                $this->messageManager->addSuccess(__($msg));
            } else {
                // Error message
                $msg = 'Something went wrong trying to create the Zillion order. ';
                $msg .= 'Please retry sent Zillion post order request from the shipment details view.';
                $this->messageManager->addError(__($msg));
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $shipment;
    }

    /**
     * Save Status shipment post Order
     *
     * @param integer $orderId
     * @param integer $shipmentId
     * @param string $status
     * @return void
     */
    protected function saveStatusShipment($orderId, $shipmentId, $status)
    {
        $this->shipmentPostRequest->setData('shipment_id', $shipmentId);
        $this->shipmentPostRequest->setData('order_id', $orderId);
        $this->shipmentPostRequest->setData('post_order_status', $status);
        $this->shipmentPostRequest->save();
    }

    /**
     * Validate status response
     *
     * @param mixed $response
     * @return string
     */
    protected function validateStatusOrderPostResponse($response)
    {
        $status = ($response === 'Order created') ? 'success' : 'fail';
        return $status;
    }

    /**
     * Checks if it the order post can be submitted to the API for the $shipment
     *
     * @param ShipmentInterface $shipment
     * @return boolean
     */
    protected function canRequestPostOrder($shipment)
    {
        $canRequest = false;
        try {
            $order = $shipment->getOrder();
            $quoteId = $order->getQuoteId();
            $offerResponseIsValid = $this->helper->quoteOfferIsValid($quoteId);

            // Check customer request insurance
            if ($offerResponseIsValid) {
                // get data responses for shipments
                $orderResponse = $order->getData(MyZillionHelper::ORDER_POST_RESPONSE);

                // if empty just save response.
                if (empty($orderResponse)) {
                    return true;
                }

                $orderResponse = $this->serializer->unserialize($orderResponse);
                $shipmentId = $shipment->getId();
                $canRequest = !$this->helper->checkShipmentHaveResponseInOrder($orderResponse, $shipmentId);
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $canRequest;
    }

    /**
     * return data
     *  {
     *      shipment_id: integer,
     *      timestamp: timestamp,
     *      response: string | array,
     *      error: boolean
     *  }
     *
     * @param string $orderPostResponse
     * @param integer $shipmentId
     * @return array
     */
    private function armedResponseForShipping($orderPostResponse, $shipmentId)
    {
        $response = [];
        $response['shipment_id'] = $shipmentId;
        $response['timestamp'] = $this->datem2->gmtDate();
        $response['error'] = $orderPostResponse === self::SUCCESS_RESPONSE ? false : true;
        $response['response'] = $orderPostResponse;

        return $response;
    }
}
