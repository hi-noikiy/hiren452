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
 * @package     Plumrocket SMTP
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Smtp\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var string
     */
    const CONFIGURATION_SECTION = 'configuration';

    /**
     * @var string
     */
    const TEST_EMAIL_SECTION = 'test_email';

    /**
     * @var string
     */
    const DEVELOPER_SECTION = 'developer';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Helper\Context            $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        parent::__construct($context);

        $this->encryptor = $encryptor;
    }

    /**
     * Retrieve config value according to current section identifier
     *
     * @param string $path
     * @param string|int $store
     * @return mixed
     */
    private function getConfigForCurrentSection($path, $store = null, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            Data::SECTION_ID  . '/' . $path,
            $scope,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getHost($store = null)
    {
        return $this->getConfigForCurrentSection(
            self::CONFIGURATION_SECTION . '/host',
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getPort($store = null)
    {
        return $this->getConfigForCurrentSection(
            self::CONFIGURATION_SECTION . '/port',
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getEncryption($store = null)
    {
        return $this->getConfigForCurrentSection(
            self::CONFIGURATION_SECTION . '/encryption',
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getAuthentication($store = null)
    {
        return $this->getConfigForCurrentSection(
            self::CONFIGURATION_SECTION . '/authentication',
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getUsername($store = null)
    {
        return $this->getConfigForCurrentSection(
            self::CONFIGURATION_SECTION . '/username',
            $store
        );
    }

    /**
     * @param null $store
     * @return string
     */
    public function getPassword($store = null)
    {
        $encryptValue = $this->getConfigForCurrentSection(
            self::CONFIGURATION_SECTION . '/password',
            $store
        );

        return $this->encryptor->decrypt($encryptValue);
    }

    // Test Email Section

    /**
     * @param null $store
     * @return mixed
     */
    public function getTemplate($store = null)
    {
        return $this->getConfigForCurrentSection(
            self::TEST_EMAIL_SECTION . '/template',
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getFrom($store = null)
    {
        return $this->getConfigForCurrentSection(
            self::TEST_EMAIL_SECTION . '/from',
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getTo($store = null)
    {
        return $this->getConfigForCurrentSection(
            self::TEST_EMAIL_SECTION . '/to',
            $store
        );
    }

    // Developer Section

    /**
     * @param null $store
     * @return bool
     */
    public function getEmailSendingEnabled($store = null)
    {
        return (bool) $this->getConfigForCurrentSection(
            self::DEVELOPER_SECTION . '/email_sending',
            $store
        );
    }

    /**
     * @param null $store
     * @return bool
     */
    public function getLogEnabled($store = null)
    {
        return (bool) $this->getConfigForCurrentSection(
            self::DEVELOPER_SECTION . '/log',
            $store
        );
    }

    /**
     * @param null $store
     * @return int
     */
    public function getClearEmailLogDays($store = null)
    {
        return (int) $this->getConfigForCurrentSection(
            self::DEVELOPER_SECTION . '/clear_email_log',
            $store
        );
    }
}
