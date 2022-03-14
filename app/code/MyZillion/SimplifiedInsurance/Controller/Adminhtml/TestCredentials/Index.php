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

namespace MyZillion\SimplifiedInsurance\Controller\Adminhtml\TestCredentials;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MyZillion\InsuranceApi\Client;
use Magento\Framework\Exception\LocalizedException;

/**
 * Controller to handle the test credential action
 *
 * @author Exequiel Lares <exequiel@serfe.com>
 */
class Index extends Action
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
     * @param Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper
     */
    public function __construct(
        Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper
    ) {
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->objectManager = $objectManager;
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $response['status'] = true;

        try {
            // Example offer
            $offer = [
                'items'    => [
                    [
                        'quantity' => '2',
                        'type'     => 'bracelet',
                        'value'    => '1111.11',
                    ],
                ],
                'order_id' => '8',
                'zip_code' => '37421',
            ];

            // api_key in input or saved db
            $params = $this->getRequest()->getParams();
            $websiteId = $this->getRequest()->getParam('website') ?: 0;
            if ($params['api_key'] == '******') {
                $params['api_key'] = $this->configHelper->getCredentials($websiteId)['api_key'];
                if (!isset($params['api_key'])) {
                    throw new LocalizedException(__('api_key not set.'));
                }
            }

            $data['credentials']['api_key'] = $params['api_key'];
            $data['useProductionApi'] = !$params['mode'];

            $this->insuranceClient = $this->objectManager->create(Client::class, $data);

            // Check erros
            // 1 Errors detected by the myzillion-php wrapper
            // 401 Myzillion endpoint authentication failure
            $check = $this->insuranceClient->getOffer($offer);
            if (isset($check['errors'])) {
                $errorCode = $check['errors']['code'];
                if (in_array($errorCode, [1, 401])) {
                    $response['status'] = false;
                    $response['message'] = $check;
                }
            }
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            $this->logger->critical($e->getMessage());
        }

        // degug modo test
        if ($params['mode'] != 1) {
            unset($response['message']);
        }

        return $this->jsonResponse($response);
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
