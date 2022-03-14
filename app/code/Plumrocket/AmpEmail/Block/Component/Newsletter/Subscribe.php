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
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\AmpEmail\Block\Component\Newsletter;

/**
 * @method getCmsBlockVersion()
 * @method getCmsBlockIdentifier()
 * @method getCmsBlockTemplate()
 */
class Subscribe extends \Plumrocket\AmpEmailApi\Block\AbstractComponent
{
    /**
     * @var string
     */
    protected $styleFileId = 'Plumrocket_AmpEmail::css/component/:version/newsletter/subscribe.css';

    /**
     * @var array
     */
    protected $ampStates = [
        'subscriber' => []
    ];

    /**
     * @return string
     */
    public function getCheckUrl() : string
    {
        return $this->getAmpApiUrl('amp-email-api/V1/newsletter_subscriber_check');
    }

    /**
     * @return string
     */
    public function getFormUrl() : string
    {
        return $this->getAmpApiUrl('amp-email-api/V1/newsletter_subscriber_subscribeAndConfirm');
    }

    /**
     * @return string
     */
    public function getStoreUrl() : string
    {
        return $this->getFrontUrl();
    }

    /**
     * Retrieve html for follow us section
     *
     * @return string
     */
    public function getAdditionalHtml() : string
    {
        /** @var \Plumrocket\AmpEmail\Block\Component\Cms\Block $component */
        try {
            $component = $this->getLayout()->createBlock(\Plumrocket\AmpEmail\Block\Component\Cms\Block::class);
            $component->setVersion((int) $this->getCmsBlockVersion());
            $component->setBlockId((string) $this->getCmsBlockIdentifier());
            $component->setTemplate((string) $this->getCmsBlockTemplate());
            $component->setComponentPartsCollector($this->getComponentPartsCollector());

            return $component->toHtml();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->warning($e->getLogMessage());
            return '';
        }
    }

    /**
     * @return string
     */
    public function getCustomerName() : string
    {
        if ($this->getEmailTemplateVars('customerName')) {
            return (string) $this->getEmailTemplateVars('customerName');
        }

        if ($customer = $this->getEmailTemplateVars('customer')) {
            if ($customer instanceof \Magento\Customer\Model\Data\CustomerSecure
                || $customer instanceof \Magento\Customer\Model\Data\Customer
                || $customer instanceof \Magento\Customer\Model\Customer
            ) {
                return $customer->getName();
            }
        }

        return '';
    }
}
