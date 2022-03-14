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

namespace MyZillion\SimplifiedInsurance\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Helper to retrieve configurations
 */
class ConfigHelper extends AbstractHelper
{
    const API_KEY = 'myzillion_api/settings/api_key';
    const TEST_MODE_PATH = 'myzillion_api/settings/test_mode';
    const APPLY_INSURANCE_PATH = 'myzillion_api/settings/apply_insurance';
    const SUBMIT_POLICY_PATH = 'myzillion_api/settings/submit_policy';
    const DEFAULT_PRODUCT_TYPE_SOURCE = 'myzillion_api/attribute_mapping/product_source_type';
    const DEFAULT_PRODUCT_TYPE = 'myzillion_api/attribute_mapping/product_type';
    const ENABLED_PATH = 'myzillion_api/settings/enabled';
    const ZILLION_OFFER_TYPE_PATH = 'myzillion_api/settings/offer_type';
    const DEBUG_ENABLED_PATH = 'myzillion_api/debug/enabled';
    const ZILLION_DESCRIPTION_MAP = 'myzillion_api/attribute_mapping/description_map';
    const ZILLION_DESCRIPTION_FULL_MAP = 'myzillion_api/attribute_mapping/description_full_map';
    const ZILLION_DESCRIPTION_SHORT_MAP = 'myzillion_api/attribute_mapping/description_short_map';
    const ZILLION_CERTIFICATION_TYPE_MAP = 'myzillion_api/attribute_mapping/certification_type_map';
    const ZILLION_CERTIFICATION_NUMBER_MAP = 'myzillion_api/attribute_mapping/certification_number_map';
    const ZILLION_SERIAL_NUMBER_MAP = 'myzillion_api/attribute_mapping/serial_number_map';
    const ZILLION_MODEL_NUMBER_MAP = 'myzillion_api/attribute_mapping/model_number_map';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @return void
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;
        parent::__construct($context);
    }

    /**
     * Get configured credentials for API
     *
     * @param integer|string $websiteId
     * @return array|null
     */
    public function getCredentials($websiteId = 0)
    {
        $apiKey = $this->scopeConfig->getValue(self::API_KEY, ScopeInterface::SCOPE_WEBSITE, $websiteId);
        if ($apiKey) {
            return ['api_key' => $this->encryptor->decrypt($apiKey)];
        }

        return null;
    }

    /**
     * Returns test mode
     *
     * @param integer|string $websiteId
     * @return boolean
     */
    public function getTestMode($websiteId = 0)
    {
        $testMode = (boolean) $this->scopeConfig->getValue(
            self::TEST_MODE_PATH,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $testMode;
    }

    /**
     * Returns apply insurance
     *
     * @param integer|string $websiteId
     * @return string
     */
    public function getApplyInsurance($websiteId = 0)
    {
        $configuredValue = (int) $this->scopeConfig->getValue(
            self::APPLY_INSURANCE_PATH,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        if ($configuredValue === 1) {
            $applyInsurance = '2';
        } else {
            $applyInsurance = '1';
        }

        return $applyInsurance;
    }

    /**
     * Returns default product type
     *
     * @param integer|string $websiteId
     * @return string
     */
    public function getProductTypeSource($websiteId = 0)
    {
        $configuredValue = (string) $this->scopeConfig->getValue(
            self::DEFAULT_PRODUCT_TYPE_SOURCE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }

    /**
     * Returns default product type
     *
     * @param integer|string $websiteId
     * @return string
     */
    public function getDefaultProductType($websiteId = 0)
    {
        $configuredValue = (string) $this->scopeConfig->getValue(
            self::DEFAULT_PRODUCT_TYPE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }

    /**
     * Returns Zillion offer type from settings
     *
     * @param integer|string $websiteId
     * @return string
     */
    public function getZillionOfferType($websiteId = 0)
    {
        $configuredValue = (string) $this->scopeConfig->getValue(
            self::ZILLION_OFFER_TYPE_PATH,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }

    /**
     * Returns Zillion offer type field from offer type settings
     * @param interger|string $websiteId
     * @return string
     */
    public function getZillionOfferTypeFieldName($websiteId = 0)
    {
        $typeFieldName = 'binder_requested';
        $settingType = $this->getZillionOfferType($websiteId);
        if ($settingType == 'quote') {
            $typeFieldName = 'quote_requested';
        }

        return $typeFieldName;
    }

    /**
     * Returns module is enable
     *
     * @param integer|string $websiteId
     * @return boolean
     */
    public function isEnabled($websiteId = 0)
    {
        $configuredValue = (boolean) $this->scopeConfig->getValue(
            self::ENABLED_PATH,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }

    /**
     * Returns module debug is enabled
     *
     * @param integer|string $websiteId
     * @return boolean
     */
    public function isDebugEnabled($websiteId = 0)
    {
        $configuredValue = (boolean) $this->scopeConfig->getValue(
            self::DEBUG_ENABLED_PATH,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }

    /**
     * Returns Zillion description map attribute setting value
     *
     * @param integer|string $websiteId
     * @return string
     */
    public function getZillionDescriptionAttribute($websiteId = 0)
    {
        $configuredValue = (string) $this->scopeConfig->getValue(
            self::ZILLION_DESCRIPTION_MAP,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }

    /**
     * Returns Zillion description_full map attribute setting value
     *
     * @param integer|string $websiteId
     * @return string
     */
    public function getZillionDescriptionFullAttribute($websiteId = 0)
    {
        $configuredValue = (string) $this->scopeConfig->getValue(
            self::ZILLION_DESCRIPTION_FULL_MAP,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }

    /**
     * Returns Zillion description_short map attribute setting value
     *
     * @param integer|string $websiteId
     * @return string
     */
    public function getZillionDescriptionShortAttribute($websiteId = 0)
    {
        $configuredValue = (string) $this->scopeConfig->getValue(
            self::ZILLION_DESCRIPTION_SHORT_MAP,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }

    /**
     * Returns Zillion certification_type map attribute setting value
     *
     * @param integer|string $websiteId
     * @return string
     */
    public function getZillionCertificationTypeAttribute($websiteId = 0)
    {
        $configuredValue = (string) $this->scopeConfig->getValue(
            self::ZILLION_CERTIFICATION_TYPE_MAP,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }

    /**
     * Returns Zillion certification_number map attribute setting value
     *
     * @param integer|string $websiteId
     * @return string
     */
    public function getZillionCertificationNumberAttribute($websiteId = 0)
    {
        $configuredValue = (string) $this->scopeConfig->getValue(
            self::ZILLION_CERTIFICATION_NUMBER_MAP,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }

    /**
     * Returns Zillion serial_number map attribute setting value
     *
     * @param integer|string $websiteId
     * @return string
     */
    public function getZillionSerialNumberAttribute($websiteId = 0)
    {
        $configuredValue = (string) $this->scopeConfig->getValue(
            self::ZILLION_SERIAL_NUMBER_MAP,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }

    /**
     * Returns Zillion model_number map attribute setting value
     *
     * @param integer|string $websiteId
     * @return string
     */
    public function getZillionModelNumberAttribute($websiteId = 0)
    {
        $configuredValue = (string) $this->scopeConfig->getValue(
            self::ZILLION_MODEL_NUMBER_MAP,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $configuredValue;
    }
}
