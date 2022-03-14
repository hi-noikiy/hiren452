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

namespace MyZillion\SimplifiedInsurance\Controller\Ajax;

/**
 * Controller to request an offer
 */
class Offer extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \MyZillion\SimplifiedInsurance\Api\ValidateQuoteInterface
     */
    private $validateQuoteInterface;

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
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \MyZillion\SimplifiedInsurance\Api\ValidateQuoteInterface $validateQuoteInterface
     * @param \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger,
        \MyZillion\SimplifiedInsurance\Api\ValidateQuoteInterface $validateQuoteInterface,
        \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->validateQuoteInterface = $validateQuoteInterface;
        $this->configHelper = $configHelper;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $websiteId = $this->getCurrentWebsiteId();
            if (!$this->configHelper->isEnabled($websiteId)) {
                return $this->jsonResponse(['is_insurable' => false]);
            }

            $requestContent = $this->_request->getContent();
            $contentArray = $requestContent ? $this->jsonHelper->jsonDecode($requestContent) : [];

            $postCodeFrontEnd = (isset($contentArray['postcodeJs']) ? $contentArray['postcodeJs'] : '');

            $offer = $this->validateQuoteInterface->calculateInsurance($postCodeFrontEnd);

            $isInsurable = ($offer > 0) ? true : false;
            $response = [
                'is_insurable' => $isInsurable,
                'offer_value'  => $offer,
                'postcode' => $postCodeFrontEnd
            ];
        } catch (\Exception $e) {
            $response = [
                'is_insurable' => false,
                'error'        => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
            ];
            $this->logger->error(get_class($this), ['exception' => $e]);
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

    /**
     * Retrieve curent website Id
     * @return string|integer
     */
    private function getCurrentWebsiteId()
    {
        return $this->storeManager->getStore()->getWebsiteId();
    }
}
