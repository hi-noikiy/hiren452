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

namespace Plumrocket\AmpEmail\Model\Security;

class IsVerifiedSender implements \Plumrocket\AmpEmail\Api\IsVerifiedSenderInterface
{
    /**
     * @var string[]
     */
    private $testAmpSourceOrigins;

    /**
     * @var \Plumrocket\AmpEmail\Api\GetVerifiedSenderListInterface
     */
    private $getVerifiedSenderList;

    /**
     * IsVerifiedSender constructor.
     *
     * @param \Plumrocket\AmpEmail\Api\GetVerifiedSenderListInterface $getVerifiedSenderList
     * @param array                                                   $testAmpSourceOrigins
     */
    public function __construct(
        \Plumrocket\AmpEmail\Api\GetVerifiedSenderListInterface $getVerifiedSenderList,
        array $testAmpSourceOrigins = []
    ) {
        $this->getVerifiedSenderList = $getVerifiedSenderList;
        $this->testAmpSourceOrigins = array_keys(array_filter($testAmpSourceOrigins));
    }

    /**
     * @param string $email
     * @param bool   $allowRequestFromAmpPlayground
     * @return bool
     */
    public function execute(string $email, bool $allowRequestFromAmpPlayground = false) : bool
    {
        $allowedSenders = $this->getVerifiedSenderList->execute();
        if ($allowRequestFromAmpPlayground) {
            $allowedSenders = array_merge($allowedSenders, $this->testAmpSourceOrigins);
        }

        return in_array($email, $allowedSenders, true);
    }
}
