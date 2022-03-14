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

namespace Plumrocket\AmpEmail\Plugin\Magento\Email\Model;

use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * Save custom fields with started with "pramp_email_" string
 *
 * @package Plumrocket\AmpEmail\Plugin\Magento\Email\Model
 */
class BackendTemplatePlugin
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * BackendTemplatePlugin constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param \Magento\Email\Model\BackendTemplate $backendTemplate
     */
    public function beforeSave(\Magento\Email\Model\BackendTemplate $backendTemplate)
    {
        if ('adminhtml_email_template_save' === $this->request->getFullActionName()) {
            foreach ($this->request->getParams() as $key => $value) {
                if (0 === strpos($key, 'pramp_email_')) {
                    /**
                     * Create setter name for compatibility with plugins functionality
                     */
                    $methodName = 'set' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($key);
                    $backendTemplate->$methodName($value);
                }
            }
        }
    }
}
