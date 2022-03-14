<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Helper;

use Plumrocket\Newsletterpopup\Helper\Data as DataHelper;
use Magento\Framework\App\Area;

/**
 * Class Config use for retrieve module configuration
 */
class Config extends \Plumrocket\Base\Helper\Main
{
    const GOOGLE_RECAPTCHA  = 'google_recaptcha';

    /**
     * @var \Magento\Framework\Encryption\Encryptor
     */
    private $encryptor;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface  $objectManager
     * @param \Magento\Framework\App\Helper\Context      $context
     * @param \Magento\Framework\Encryption\Encryptor    $encryptor
     * @param \Magento\Framework\App\State               $state
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Framework\App\State $state,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->encryptor = $encryptor;
        $this->state = $state;
        parent::__construct($objectManager, $context);
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve config value according to current section identifier
     *
     * @param string $path
     * @param string|int $store
     * @param null $scope
     * @return mixed
     */
    public function getSectionConfig($path, $store = null, $scope = null)
    {
        return $this->getConfig(
            DataHelper::SECTION_ID . '/' . $path,
            $store,
            $scope
        );
    }

    /**
     * @param null $store
     * @return string
     */
    public function getReCaptchaSiteKey($store = null)
    {
        return (string)trim($this->getSectionConfig(self::GOOGLE_RECAPTCHA . '/google_recaptcha_sitekey', $store));
    }

    /**
     * @param null $store
     * @return string
     */
    public function getReCaptchaSecretKey($store = null)
    {
        return (string)trim($this->encryptor->decrypt(
            $this->getSectionConfig(self::GOOGLE_RECAPTCHA . '/google_recaptcha_secretkey', $store)
        ));
    }

    /**
     * @return string
     */
    public function getConstantContactApiKey()
    {
        return $this->encryptor->decrypt(
            $this->getSectionConfig('integration/constantcontact/key')
        );
    }

    /**
     * @return string
     */
    public function getConstantContactSecret()
    {
        return $this->encryptor->decrypt(
            $this->getSectionConfig('integration/constantcontact/secret')
        );
    }

    /**
     * @return string
     */
    public function getConstantContactRefreshToken()
    {
        return $this->encryptor->decrypt(
            $this->getSectionConfig('integration/constantcontact/refresh_token')
        );
    }

    /**
     * @return string
     */
    public function getConstantContactAccessToken()
    {
        return $this->encryptor->decrypt(
            $this->getSectionConfig('integration/constantcontact/access_token')
        );
    }

    /**
     * Retrieve constant contact redirect uri
     *
     * @return string|null
     */
    public function getConstantContactRedirectUri()
    {
        $params = ['_nosid' => true];

        if (Area::AREA_ADMINHTML === $this->state->getAreaCode()) {
            $request = $this->_getRequest();
            $storeId = $request->getParam('store');

            if (! $storeId) {
                $websiteId = $request->getParam('website');
                $storeId = $this->storeManager
                    ->getWebsite($websiteId)
                    ->getDefaultGroup()
                    ->getDefaultStoreId();

                if (! $storeId) {
                    $storeId = $this->storeManager
                        ->getWebsite(true)
                        ->getDefaultGroup()
                        ->getDefaultStoreId();
                }
            }

            $params['key'] = false;
            $params['_scope'] = $storeId;
        }

        return $this->_getUrl(
            'prnewsletterpopup/accessToken/constantContact',
            $params
        );
    }

    /**
     * @return string
     */
    public function getSalesForceAccessToken()
    {
        return $this->encryptor->decrypt(
            $this->getSectionConfig('integration/salesforce/access_token')
        );
    }

    /**
     * @return string
     */
    public function getSalesForceClientId()
    {
        return $this->encryptor->decrypt(
            $this->getSectionConfig('integration/salesforce/user_id')
        );
    }

    /**
     * @return string
     */
    public function getSalesForceClientSecret()
    {
        return $this->encryptor->decrypt(
            $this->getSectionConfig('integration/salesforce/secret_id')
        );
    }

    /**
     * @return string
     */
    public function getSalesForceUsername()
    {
        return $this->encryptor->decrypt(
            $this->getSectionConfig('integration/salesforce/app_name')
        );
    }

    /**
     * @return string
     */
    public function getSalesForcePassword()
    {
        return $this->encryptor->decrypt(
            $this->getSectionConfig('integration/salesforce/api_password')
        );
    }

    /**
     * @return string
     */
    public function getSalesForceSecurityToken()
    {
        return $this->encryptor->decrypt(
            $this->getSectionConfig('integration/salesforce/api_security_token')
        );
    }
    
}
