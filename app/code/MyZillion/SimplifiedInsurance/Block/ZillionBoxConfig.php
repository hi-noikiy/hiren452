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

namespace MyZillion\SimplifiedInsurance\Block;

use Magento\Framework\Serialize\SerializerInterface;
use MyZillion\SimplifiedInsurance\Helper\ConfigHelper;

/**
 * Zillion box block class
 *
 * @author Exequiel Lares <exequiel@serfe.com>
 */
class ZillionBoxConfig extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session                $checkoutSession
     * @param \Magento\Quote\Model\QuoteIdMaskFactory        $quoteIdMaskFactory
     * @param \Magento\Customer\Model\Session                $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface     $storeManager
     * @param SerializerInterface                        $serializer
     * @param ConfigHelper                               $configHelper
     * @param array                                      $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        SerializerInterface $serializer,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
        $this->configHelper = $configHelper;
    }

    /**
     * Retrieve current quote Id
     *
     * @return string|integer
     */
    public function getQuoteId()
    {
        $quote = $this->checkoutSession->getQuote();
        $quoteId = null;
        if ($quote) {
            $quoteId = $quote->getId();
            if (!$this->customerSession->isLoggedIn()) {
                $quoteId = $this->getMaskedId($quoteId);
            }
        }

        return $quoteId;
    }

    /**
     * Return if current customer is logged in
     *
     * @return boolean
     */
    public function getIsLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Retrieve current store code
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }

    /**
     * Retrieve current website id
     *
     * @return string|integer
     */
    public function getWebsiteId()
    {
        return $this->storeManager->getStore()->getWebsiteId();
    }

    /**
     * Retrieve Zillion box configuration as a JSON string
     * Used to initalize Zillion box KnockoutJS components
     *
     * @return string
     */
    public function getConfigJson()
    {
        $websiteId = $this->getWebsiteId();
        $config = [
            'is_enabled' => $this->configHelper->isEnabled($websiteId),
            'offer_type' => $this->configHelper->getZillionOfferType($websiteId),
        ];
        return $this->serializer->serialize($config);
    }

    /**
     * Retrieve guest masked id
     *
     * @param  integer|string $quoteId
     * @return string
     */
    protected function getMaskedId($quoteId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'quote_id');
        $id = $quoteIdMask->getMaskedId();
        return $id;
    }
}
