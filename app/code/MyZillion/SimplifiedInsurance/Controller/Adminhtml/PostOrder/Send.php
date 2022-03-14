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
 * @author Maximo Marucci | Serfe Team <info@serfe.com>
 * @copyright 2020 Zillion Insurance Services, Inc.
 * @since 1.0.0
 */

namespace MyZillion\SimplifiedInsurance\Controller\Adminhtml\PostOrder;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MyZillion\InsuranceApi\Client;
use Magento\Framework\Exception\LocalizedException;
use MyZillion\SimplifiedInsurance\Model\ResourceModel\ShipmentPostRequest\Collection as ShipmentPostCollection;

/**
 * Controller retry create order
 *
 * @author Maximo Marucci | Serfe Team <info@serfe.com>
 */
class Send extends Action
{

    /**
     * @var \MyZillion\InsuranceApi\Client
     */
    private $insuranceClient;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \MyZillion\SimplifiedInsurance\Helper\ConfigHelper
     */
    private $configHelper;

    /**
     * @var ShipmentPostCollection
     */
    private $shipmentPostRequestCollection;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \MyZillion\SimplifiedInsurance\Api\AdapterInterface
     */
    private $insuranceAdapter;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @param Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper
     * @param ShipmentPostCollection $shipmentPostRequestCollection
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \MyZillion\SimplifiedInsurance\Api\AdapterInterface $insuranceAdapter
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper,
        ShipmentPostCollection $shipmentPostRequestCollection,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \MyZillion\SimplifiedInsurance\Api\AdapterInterface $insuranceAdapter,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->objectManager = $objectManager;
        $this->configHelper = $configHelper;
        $this->shipmentPostRequestCollection = $shipmentPostRequestCollection;
        $this->messageManager = $messageManager;
        $this->insuranceAdapter = $insuranceAdapter;
        $this->shipmentRepository = $shipmentRepository;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        try {
            $response = [];
            $params = $this->getRequest()->getParams();

            // Check Params
            if (empty($params)) {
                throw new LocalizedException(
                    __('Request required parameters are missing'),
                    null,
                    400
                );
            } else {
                if (!isset($params['id'])) {
                    throw new LocalizedException(
                        __("Missing required parameter 'id'."),
                        null,
                        400
                    );
                }
            }

            $shipmentId = $params['id'];

            // Check shipment_id status
            $shipmentPostRequest = $this->shipmentPostRequestCollection
                ->addFieldToFilter('shipment_id', $shipmentId)
                ->getFirstItem();

            if (!$shipmentPostRequest) {
                throw new NoSuchEntityException(
                    __('Zillion post order request information was not found for the required shipping id.'),
                    null,
                    400
                );
            }

            $shipmentPostRequestData = $shipmentPostRequest->getData();
            $status = $shipmentPostRequestData['post_order_status'];
            $responseMapper = [];
            // If post order is not created for the shipment
            if ($status !== 'success') {
                // Try to create the post order. Sent information to Zillion
                $shipmentData = $this->shipmentRepository->get($shipmentId);
                if ($shipmentData->hasData() === true) {
                    $storeId = $shipmentData->getStoreId();
                    $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
                    $this->insuranceAdapter->initialize($websiteId);
                    $orderPostResponse = $this->insuranceAdapter->sendPostOrderRequest($shipmentData);
                }

                // If post order was created. Update shipment status
                if ($orderPostResponse === 'Order created') {
                    $shipmentPostRequest->setData('post_order_status', 'success');
                    $shipmentPostRequest->save();
                    $this->messageManager->addSuccess(
                        __('The shipment information was successfully sent to Zillion')
                    );
                }
            } else {
                $this->messageManager->addInfo(
                    __('No information sent to Zillion. The Zillion order already exists for this shipment')
                );
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $this->_redirect(
            'adminhtml/order_shipment/view',
            ['shipment_id' => $shipmentId]
        );
    }

    /**
     * Create json response
     *
     * @param mixed $response
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
