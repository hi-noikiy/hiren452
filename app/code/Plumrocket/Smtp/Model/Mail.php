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

namespace Plumrocket\Smtp\Model;

use Plumrocket\Smtp\Helper\Data;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend_Exception;
use Zend_Mail_Transport_Smtp;

class Mail
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var
     */
    protected $message;

    /**
     * @var
     */
    protected $transport;

    /**
     * Mail constructor.
     *
     * @param Data $helper
     *
     */
    public function __construct(Data $helper)
    {
        $this->dataHelper = $helper;
    }

    /**
     * @param $storeId
     * @return Smtp|Zend_Mail_Transport_Smtp
     * @throws Zend_Exception
     */
    public function getTransport($storeId)
    {
        if (null !== $this->transport) {
            return $this->transport;
        }

        if (! $this->dataHelper->hostExist($storeId)) {
            throw new Zend_Exception(__('The host is not specified. Please check the extension configuration.'));
        }

        if ($this->dataHelper->newVersion()) {
            $optionsNewVersions = $this->dataHelper->getOptionsForNewVersion($storeId);

            $options = new SmtpOptions($optionsNewVersions);
            $this->transport = new Smtp($options);
        } else {
            $optionsOldVersions = $this->dataHelper->getOptionForOldVersion($storeId);

            $this->transport = new Zend_Mail_Transport_Smtp(
                $optionsOldVersions['host'],
                $optionsOldVersions
            );
        }

        return $this->transport;
    }

    /**
     * @return Data
     */
    public function getDataHelper()
    {
        return $this->dataHelper;
    }
}
