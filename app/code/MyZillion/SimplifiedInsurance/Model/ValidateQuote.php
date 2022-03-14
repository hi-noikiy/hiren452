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

namespace MyZillion\SimplifiedInsurance\Model;

use MyZillion\SimplifiedInsurance\Helper\Data as MyZillionHelper;

/**
 * Validate Quote model
 */
class ValidateQuote implements \MyZillion\SimplifiedInsurance\Api\ValidateQuoteInterface
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \MyZillion\SimplifiedInsurance\Api\AdapterInterface
     */
    private $insuranceAdapter;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \MyZillion\SimplifiedInsurance\Api\AdapterInterface $insuranceAdapter
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \MyZillion\SimplifiedInsurance\Api\AdapterInterface $insuranceAdapter,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->insuranceAdapter = $insuranceAdapter;
        $this->storeManager = $storeManager;
    }

    /**
     * calculateInsurance method
     * @param string $postCodeFrontEnd
     * @return float
     */
    public function calculateInsurance(string $postCodeFrontEnd = '')
    {
        $quote = $this->checkoutSession->getQuote();
        $storeId = $quote->getStoreId();
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        // Initialize adapter with proper configuration
        $this->insuranceAdapter->initialize($websiteId);
        $offer = $this->insuranceAdapter->getOffer($quote, $postCodeFrontEnd);
        $insurance = $this->insuranceAdapter->parseOfferResponse($offer);
        $offerResponse = $this->serializer->serialize($offer);
        $quote->setData(MyZillionHelper::OFFER_RESPONSE, $offerResponse);
        $this->quoteRepository->save($quote);

        return $insurance;
    }
}
