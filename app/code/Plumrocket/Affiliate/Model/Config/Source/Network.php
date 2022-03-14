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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Config\Source;

/**
 * Integration status options.
 */
class Network implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Plumrocket\Affiliate\Model\TypeFactory
     */
    protected $_typeFactory;

    /**
     * @param \Plumrocket\Affiliate\Model\TypeFactory $typeFactory 
     */
    public function __construct(
        \Plumrocket\Affiliate\Model\TypeFactory $typeFactory
    ) {
        $this->_typeFactory = $typeFactory;
    }

    /**
     * Retrieve status options array.
     *
     * @return array
     */
    public function toOptionArray()
    {

        $types = $this->_typeFactory->create()->getCollection();
        $result = [ 0 => ['value' => '', 'label' => '&nbsp;']];

        foreach ($types as $type) {
            $result[] = ['value' => $type->getId(), 'label' => $type->getName()];
        }

        return $result;
    }
}
