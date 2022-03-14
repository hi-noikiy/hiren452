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

namespace Plumrocket\Affiliate\Block\Adminhtml\Order;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Plumrocket\Affiliate\Model\Order\InfoFactory as OrderInfoFactory;
use Plumrocket\Affiliate\Model\AffiliateFactory;
use Magento\Framework\Registry;

class Info extends Template
{
    protected $_info;
    protected $_affiliateNetworks;
    protected $_orderInfoFactory;
    protected $_affiliateFactory;
    protected $_registry;

    public function __construct(
        Context $context,
        OrderInfoFactory $orderInfoFactory,
        AffiliateFactory $affiliateFactory,
        Registry $registry,
        array $data = []
    ) {
        $this->_orderInfoFactory = $orderInfoFactory;
        $this->_affiliateFactory = $affiliateFactory;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    public function getOrder()
    {
        return $this->_registry->registry('current_order');
    }

    public function getInfo()
    {
        if ($this->_info === null) {
            $oder = $this->getOrder();
            $this->_info = $this->_orderInfoFactory
                ->create()
                ->load(
                    $this->getOrder()->getId(),
                    'order_id'
                );
        }

        return $this->_info;
    }

    public function getAffiliateNetworks()
    {
        if ($this->_affiliateNetworks === null) {
            $this->_affiliateNetworks = $this->_affiliateFactory
                ->create()
                ->getCollection()
                ->addStoreToFilter($this->getOrder()->getStoreId());
        }
        return $this->_affiliateNetworks;
    }
}
