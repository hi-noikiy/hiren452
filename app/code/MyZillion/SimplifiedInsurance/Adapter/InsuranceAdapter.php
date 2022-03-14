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

namespace MyZillion\SimplifiedInsurance\Adapter;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use MyZillion\InsuranceApi\Client;

/**
 * Adapter for \MyZillion\InsuranceApi\Client
 */
class InsuranceAdapter implements \MyZillion\SimplifiedInsurance\Api\AdapterInterface
{

    /**
     * @var \MyZillion\InsuranceApi\Client
     */
    private $insuranceClient;

    /**
     * @var \MyZillion\SimplifiedInsurance\Api\MapperInterface
     */
    private $dataMapper;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \MyZillion\SimplifiedInsurance\Helper\Logger
     */
    private $zillionLogger;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var [type]
     */
    private $objectManager;

    /**
     * @var [type]
     */
    private $configHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \MyZillion\SimplifiedInsurance\Api\MapperInterface $dataMapper
     * @param \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper
     * @param \MyZillion\SimplifiedInsurance\Helper\Logger $zillionLogger
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Psr\Log\LoggerInterface $logger
     * @throws LocalizedException
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MyZillion\SimplifiedInsurance\Api\MapperInterface $dataMapper,
        \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper,
        \MyZillion\SimplifiedInsurance\Helper\Logger $zillionLogger,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->objectManager = $objectManager;
        $this->configHelper = $configHelper;
        $this->dataMapper = $dataMapper;
        $this->logger = $logger;
        $this->zillionLogger = $zillionLogger;
        $this->serializer = $serializer;
    }

    /**
     * Initialize adapter for a specific website
     *
     * @param  integer|string $websiteId [description]
     * @return void
     * @throws LocalizedException If credentials are not set
     */
    public function initialize($websiteId = 0)
    {
        $credentials = $this->configHelper->getCredentials($websiteId);
        if (!$credentials) {
            throw new LocalizedException(__('Error trying to parse API credentials. API KEY is not set'));
        }

        $isProductionMode = !$this->configHelper->getTestMode($websiteId);
        $this->insuranceClient = $this->objectManager->create(
            Client::class,
            [
                'credentials'      => $credentials,
                'useProductionApi' => $isProductionMode,
            ]
        );
    }

    /**
     * get Offer
     *
     * @param string $postCodeFrontEnd
     * @param CartInterface $quote
     * @return Client
     */
    public function getOffer(CartInterface $quote, string $postCodeFrontEnd)
    {
        $offerData = $this->dataMapper->quoteToOffer($quote, $postCodeFrontEnd);

        // Debug
        $this->zillionLogger->debug('Request: getOffer');
        $this->zillionLogger->debug('Data sent:');
        $this->zillionLogger->debug($this->serializer->serialize($offerData));

        $offer = $this->insuranceClient->getOffer($offerData);

        // Debug
        $this->zillionLogger->debug('Response:');
        $this->zillionLogger->debug($this->serializer->serialize($offer));
        return $offer;
    }

    /**
     * send post order request
     *
     * @param ShipmentInterface $shipment
     * @return string
     */
    public function sendPostOrderRequest(ShipmentInterface $shipment)
    {
        $postOrderData = $this->dataMapper->shipmentToPostOrderRequest($shipment);
        // Debug
        $this->zillionLogger->debug('Request: postOrder');
        $this->zillionLogger->debug('Data sent:');
        $this->zillionLogger->debug($this->serializer->serialize($postOrderData));

        $postOrder = $this->insuranceClient->postOrder($postOrderData);
        // Debug
        $this->zillionLogger->debug('Response:');
        $this->zillionLogger->debug($this->serializer->serialize($postOrder));
        // Just check if response has error to log them
        $this->responseHasErrors($postOrder);
        return $postOrder;
    }

    /**
     * parse Offer Response
     *
     * @param strung $response
     * @return float
     */
    public function parseOfferResponse($response)
    {
        $insurance = 0;
        if (!$this->responseHasErrors($response)) {
            $insurance = (float) $response['offer']['zillion_total_price'];
            // If has decimal values then format it
            if ($this->isDecimal($insurance)) {
                $insurance = number_format($insurance, 2);
            }
        }

        return $insurance;
    }

    /**
     * Check if a float number has decimal values
     * @param  [type]  $val [description]
     * @return boolean      [description]
     */
    protected function isDecimal($val)
    {
        if (is_numeric($val) && floor($val) != $val) {
            return true;
        }

        return false;
    }

    /**
     * Evaluates if API response has errors
     *
     * @param array $response
     * @return boolean
     */
    protected function responseHasErrors($response)
    {
        $hasErrors = false;
        if (is_array($response)
            && (isset($response['errors'])
            || (!isset($response['offer']) && !isset($response['policy'])))
        ) {
            $hasErrors = true;
            $this->logger->error(json_encode($response));
        }

        return $hasErrors;
    }
}
