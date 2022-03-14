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
 * @package   Plumrocket_AutoInvoiceShipment
 * @copyright Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AutoInvoiceShipment\Model;

use Magento\Framework\ObjectManagerInterface;

class ObjectProvider
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * ObjectProvider constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @return mixed
     */
    public function get(string $className)
    {
        return $this->objectManager->get($className);
    }

    /**
     * @param string $className
     * @return mixed
     */
    public function create(string $className)
    {
        return $this->objectManager->create($className);
    }
}
