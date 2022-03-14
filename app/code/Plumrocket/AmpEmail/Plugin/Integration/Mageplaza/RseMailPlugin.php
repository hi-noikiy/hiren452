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

namespace Plumrocket\AmpEmail\Plugin\Integration\Mageplaza;

class RseMailPlugin
{
    /**
     * @var \Plumrocket\AmpEmail\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\AmpEmail\Model\Smtp\ExtendZendOneMailTransport
     */
    private $extendZendOneMailTransport;

    /**
     * RseMailPlugin constructor.
     *
     * @param \Plumrocket\AmpEmail\Helper\Data                           $dataHelper
     * @param \Plumrocket\AmpEmail\Model\Smtp\ExtendZendOneMailTransport $extendZendOneMailTransport
     */
    public function __construct(
        \Plumrocket\AmpEmail\Helper\Data $dataHelper,
        \Plumrocket\AmpEmail\Model\Smtp\ExtendZendOneMailTransport $extendZendOneMailTransport
    ) {
        $this->dataHelper = $dataHelper;
        $this->extendZendOneMailTransport = $extendZendOneMailTransport;
    }

    /**
     * Change protected method _buildBody for \Zend_Mail_Transport_Smtp
     *
     * @param \Mageplaza\Smtp\Mail\Rse\Mail $subject
     * @param                               $resultTransport
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return \Zend_Mail_Transport_Smtp|\Plumrocket\AmpEmail\Model\Email\Old\Zend\Transport\Smtp
     */
    public function afterGetTransport(\Mageplaza\Smtp\Mail\Rse\Mail $subject, $resultTransport)
    {
        if ($resultTransport instanceof \Zend_Mail_Transport_Smtp && $this->dataHelper->moduleEnabled()) {
            $resultTransport = $this->extendZendOneMailTransport->execute($resultTransport);
        }

        return $resultTransport;
    }
}
