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

namespace Plumrocket\AmpEmail\Model\Email;

class EmailAddressParser implements EmailAddressParserInterface
{
    /**
     * @var \Magento\Framework\Validator\EmailAddress
     */
    private $emailValidator;

    public function __construct(\Magento\Framework\Validator\EmailAddress $emailValidator)
    {
        $this->emailValidator = $emailValidator;
    }

    /**
     * @param string $string
     * @return array
     */
    public function getValidEmails(string $string) : array
    {
        preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);

        $result = array_unique($matches[0]);

        $emailValidator = $this->emailValidator;

        $result = array_filter($result, static function ($email) use ($emailValidator) {
            return $emailValidator->isValid($email);
        });

        return $result;
    }
}
