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

use Magento\Quote\Api\CartRepositoryInterface;
use MyZillion\SimplifiedInsurance\Api\RequestInsuranceInterface;
use MyZillion\SimplifiedInsurance\Helper\Data as Helper;

/**
 * Request insurance model logic
 *
 * @author Exequiel Lares <exequiel@serfe.com>
 */
class RequestInsurance implements RequestInsuranceInterface
{

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * Constructor
     *
     * @param CartRepositoryInterface             $cartRepository
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Set Extra Data for guest visitor
     *
     * @param string $cartId
     * @param mixed $data
     * @return string
     */
    public function setRequestInsuranceGuest($cartId, $data)
    {
        if ($data && !empty($data)) {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            /** @var Quote $quote */
            $quote = $this->cartRepository->getActive($quoteIdMask->getQuoteId());
            $response = $this->updateQuote($quote, $data);
        } else {
            $response = [
                'cart'    => $cartId,
                'data'    => $data,
                'message' => __('Nothing to change.'),
            ];
        }

        return json_encode($response);
    }

    /**
     * Set Extra Data for registered customer
     *
     * @param string $cartId
     * @param mixed $data
     * @return string
     */
    public function setRequestInsuranceCustomer($cartId, $data)
    {
        if ($data && !empty($data)) {
            /** @var Quote $quote */
            $quote = $this->cartRepository->getActive($cartId);
            $response = $this->updateQuote($quote, $data);
        } else {
            $response = [
                'cart'    => $cartId,
                'data'    => $data,
                'message' => __('Nothing to change.'),
            ];
        }

        return json_encode($response);
    }

    /**
     * Update quote with the extra data
     * @param  \Magento\Quote\Api\Data\CartInterface $quote
     * @param  array $data
     * @return array
     */
    private function updateQuote($quote, $data)
    {
        if ($quote) {
            $fields = [Helper::CUSTOMER_REQUEST_INSURANCE];
            $changed = false;
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $quote->setData($field, $data[$field]);
                    $changed = true;
                }
            }

            if ($changed) {
                $response = [
                    'quote_id' => $quote->getId(),
                    'data'     => $data,
                    'message'  => __('Cart updated.'),
                ];
                // Save new data
                try {
                    $this->cartRepository->save($quote);
                } catch (\Exception $e) {
                    $response = [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ];
                }
            } else {
                $response = [
                    'data'    => $data,
                    'message' => __('Nothing to change.'),
                ];
            }
        } else {
            $response = [
                'data'  => $data,
                'error' => __('Error: Quote object not found.'),
            ];
        }

        return $response;
    }
}
