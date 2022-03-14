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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Observer;

class AwinServerToServer implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Plumrocket\Affiliate\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\App\RequestInterface
     * @param \Magento\Framework\Stdlib\CookieManagerInterface
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     * @param \Plumrocket\Affiliate\Helper\Data
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Plumrocket\Affiliate\Helper\Data $dataHelper
    ) {
        $this->request = $request;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->dataHelper = $dataHelper;
    }
    
    /**
     * Capturing and saving cookie awc parameter for Awin affiliate
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $awc = $this->request->getParam('awc');
        if (!$this->dataHelper->moduleEnabled() || !$awc) {
            return;
        }

        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $publicCookieMetadata->setDurationOneYear();
        $publicCookieMetadata->setPath('/');
        $publicCookieMetadata->setHttpOnly(true);
        $publicCookieMetadata->setSecure(true);
 
        $this->cookieManager->setPublicCookie(
            'awc',
            $awc,
            $publicCookieMetadata
        );
    }
}
