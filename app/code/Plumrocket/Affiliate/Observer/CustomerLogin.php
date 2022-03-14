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
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    /**
     * @var \Plumrocket\Affiliate\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    protected $_phpCookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata Factory
     */
    protected $_publicCookieMetadataFactory;

    /**
     * @param \Magento\Customer\Model\Session                              $customerSession
     * @param \Plumrocket\Affiliate\Helper\Data                            $dataHelper
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager            $phpCookieManager
     * @param \Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory $publicCookieMetadataFactory
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Plumrocket\Affiliate\Helper\Data $dataHelper,
        \Magento\Framework\Stdlib\Cookie\PhpCookieManager $phpCookieManager,
        \Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory $publicCookieMetadataFactory
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_session = $customerSession;
        $this->_phpCookieManager = $phpCookieManager;
        $this->_publicCookieMetadataFactory = $publicCookieMetadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_dataHelper->moduleEnabled()) {
            return;
        }

        $this->_setLoginSuccessMarker();
    }

    /**
     * Mark that customer has logged
     */
    protected function _setLoginSuccessMarker()
    {
        $this->_session->setPlumrocketAffiliateLoginSuccess(true);

        if ($customer = $this->_session->getCustomer()) {
            if ($email = $customer->getEmail()) {
                $email = strtolower(trim($email));
                $emailHash = md5($email); //@codingStandardsIgnoreLine
            } else {
                $emailHash = '';
            }

            $this->_phpCookieManager->setPublicCookie(
                'cutomer_email_hash',
                $emailHash,
                $this->_publicCookieMetadataFactory
                    ->create()
                    ->setHttpOnly(false)
                    ->setPath('/')
            );
        }
    }
}
