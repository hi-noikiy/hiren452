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

namespace Plumrocket\Smtp\Plugin\Framework\Mail\Template;

class TransportBuilderPlugin
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registryObject;

    /**
     * @var \Plumrocket\Smtp\Model\Mail
     */
    private $mail;

    /**
     * TransportBuilderPlugin constructor.
     *
     * @param \Magento\Framework\Registry                              $registry
     * @param \Plumrocket\Smtp\Model\Mail                              $mail
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Plumrocket\Smtp\Model\Mail $mail
    ) {
        $this->registryObject = $registry;
        $this->mail = $mail;
    }

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder $subject
     * @param                                                   $templateOptions
     * @return array
     */
    public function beforeSetTemplateOptions(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        $templateOptions
    ) {
        if ($this->mail->getDataHelper()->moduleEnabled($templateOptions['store'])) {
            $this->registryObject->unregister('plumrocket_smtp_store_id');

            if (array_key_exists('store', $templateOptions)) {
                $this->registryObject->register('plumrocket_smtp_store_id', $templateOptions['store']);
            }
        }

        return [$templateOptions];
    }
}
