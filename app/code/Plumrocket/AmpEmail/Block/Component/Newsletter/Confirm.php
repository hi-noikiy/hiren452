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

class Confirm extends \Plumrocket\AmpEmailApi\Block\AbstractComponent
{
    /**
     * @var string
     */
    protected $styleFileId = 'Plumrocket_AmpEmail::css/component/v1/newsletter/confirm.css';

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
        if (! $this->getSubscriber()) {
            return $this->getAmpApiUrl('amp-email-api/V1/newsletter_subscriber_check');
        }

        return $this->getAmpApiUrl(
            'amp-email-api/V1/newsletter_subscriber_check',
            ['id' => $this->getSubscriber()->getId()]
        );
    }

    /**
     * @return string
     */
    public function getConfirmUrl() : string
    {
        if (! $this->getSubscriber()) {
            return $this->getAmpApiUrl('amp-email-api/V1/newsletter_subscriber_confirm');
        }

        return $this->getAmpApiUrl(
            'amp-email-api/V1/newsletter_subscriber_subscribeAndConfirm',
            [
                'subscriber_id' => $this->getSubscriber()->getId(),
                'subscriber_code' => $this->getSubscriber()->getCode(),
            ]
        );
    }

    /**
     * @return \Magento\Newsletter\Model\Subscriber|null
     */
    public function getSubscriber()
    {
        return $this->getEmailTemplateVars('subscriber');
    }

    /**
     * @return string
     */
    public function getConfirmationLink() : string
    {
        return $this->getSubscriber() ? $this->getSubscriber()->getConfirmationLink() : '';
    }

    /**
     * @return string
     */
    public function getVisitStoreUrl() : string
    {
        return $this->getFrontUrl();
    }

    /**
     * @return string
     */
    protected function _toHtml() : string
    {
        if (! $this->getSubscriber()) {
            $this->_logger->critical('AmpEmail:: email template var "subscriber" not found for component Confirm.');
            return '';
        }

        return parent::_toHtml();
    }
}
