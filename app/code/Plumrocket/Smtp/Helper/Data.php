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

class Data extends Main
{
    /**
     * Section name for configs
     */
    const SECTION_ID = 'prsmtp';

    /**
     * @var string
     */
    const OLD_VERSION = '2.2.8';

     /**
      * @var string
      */
    const REFLECTION_VERSION = '2.2.0';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var string
     */
    protected $_configSectionId = self::SECTION_ID;

    /**
     * @var array
     */
    private $optionsForNewVersion;

    /**
     * @var array
     */
    private $optionsForOldVersion;

    /**
     * @var array
     */
    private $connectionData;

    /**
     * @var bool
     */
    private $forTest = false;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Stdlib\DateTime\DateTime     $dateTime
     * @param Config                                          $configHelper
     * @param \Magento\Framework\ObjectManagerInterface       $objectManager
     * @param \Magento\Framework\App\Helper\Context           $context
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        Config $configHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($objectManager, $context);

        $this->configHelper = $configHelper;
        $this->dateTime = $dateTime;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param null $store
     * @return bool
     */
    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId . '/general/enabled', $store);
    }

    /**
     * @return bool
     */
    public function canRunAutoClear()
    {
        return ($this->moduleEnabled() && $this->logEnabled() && ! empty($this->configHelper->getClearEmailLogDays()));
    }

    /**
     * @return false|int
     */
    public function getRemoveTime()
    {
        return strtotime($this->dateTime->date())
            - strtotime('+' . $this->configHelper->getClearEmailLogDays() . ' days');
    }

    /**
     * @param null $store
     * @return array
     */
    public function getOptionForOldVersion($store = null)
    {
        if (! empty($this->optionsForNewVersion)) {
            return $this->optionsForNewVersion;
        }

        $options = [];
        $connectionData = $this->getConnectionData($store);

        if ($auth = $connectionData['auth']) {
            $options['auth'] = $auth;
            $options['username'] = $connectionData['username'];
            $options['password'] = $connectionData['password'];
        }

        if ($protocol = $connectionData['ssl']) {
            $options['ssl'] = $protocol;
        }

        $options['host'] = $connectionData['host'];
        $options['port'] = $connectionData['port'];

        return $this->optionsForNewVersion = $options;
    }

    /**
     * @param null $store
     * @return array
     */
    public function getOptionsForNewVersion($store = null)
    {
        if (! empty($this->optionsForOldVersion)) {
            return $this->optionsForOldVersion;
        }

        $options = [];
        $connectionData = $this->getConnectionData($store);

        if ($auth = $connectionData['auth']) {
            $options['connection_class'] = $auth;
            $options['connection_config'] = [
                'username' => $connectionData['username'],
                'password' => $connectionData['password']
            ];
        }

        if ($protocol = $connectionData['ssl']) {
            $options['connection_config']['ssl'] = $protocol;
        }

        $options['host'] = $connectionData['host'];
        $options['port'] = $connectionData['port'];

        return $this->optionsForOldVersion = $options;
    }

    /**
     * @param $data
     */
    public function setConnectionData($data)
    {
        $password = $data['password'];

        if (empty(str_replace('*', '', $password))) {
            $password = $this->configHelper->getPassword();
        }

        $this->connectionData = [
            'auth' => $data['authentication'],
            'username' => $data['username'],
            'password' => $password,
            'ssl' => $data['encryption'],
            'host' => $data['host'],
            'port' => $data['port']
        ];

        $this->forTest = true;
    }

    /**
     * @param null $store
     * @param bool $forTest
     * @return array
     */
    public function getConnectionData($store = null)
    {
        if ($this->forTest) {
            return $this->connectionData;
        }

        return [
            'auth' => $this->configHelper->getAuthentication($store),
            'username' => $this->configHelper->getUsername($store),
            'password' => $this->configHelper->getPassword($store),
            'ssl' => $this->configHelper->getEncryption($store),
            'host' => $this->configHelper->getHost($store),
            'port' => $this->configHelper->getPort($store)
        ];
    }

    /**
     * @param $store
     * @return bool
     */
    public function hostExist($store)
    {
        return (bool)$this->configHelper->getHost($store);
    }

    /**
     * @return mixed
     */
    public function newVersion()
    {
        return version_compare(
            $this->productMetadata->getVersion(),
            self::OLD_VERSION,
            '>='
        );
    }

    /**
     * @param $store
     * @return bool
     */
    public function enableEmailSending($store)
    {
        return (bool)$this->configHelper->getEmailSendingEnabled($store);
    }

    /**
     * @param $store
     * @return bool
     */
    public function logEnabled($store = null)
    {
        return $this->configHelper->getLogEnabled($store);
    }

    /**
     * @return mixed
     */
    public function canUseReflectionClass()
    {
        return version_compare(
            $this->productMetadata->getVersion(),
            self::REFLECTION_VERSION,
            '<='
        );
    }
}
