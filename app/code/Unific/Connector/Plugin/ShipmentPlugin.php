<?php

namespace Unific\Connector\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Store\Model\App\Emulation;
use Unific\Connector\Helper\Data\Order;
use Unific\Connector\Helper\Hmac;
use Unific\Connector\Helper\Message\Queue;
use Unific\Connector\Helper\Settings;

class ShipmentPlugin extends AbstractPlugin
{
    /**
     * @var Order
     */
    protected $orderDataHelper;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var bool
     */
    protected $processAfterSave = false;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Hmac $hmacHelper
     * @param Queue $queueHelper
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleList
     * @param Order $orderDataHelper
     * @param Session $customerSession
     * @param Emulation $emulation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Hmac $hmacHelper,
        Queue $queueHelper,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        Order $orderDataHelper,
        Session $customerSession,
        Emulation $emulation
    ) {
        parent::__construct(
            $scopeConfig,
            $hmacHelper,
            $queueHelper,
            $productMetadata,
            $moduleList,
            $emulation
        );

        $this->orderDataHelper = $orderDataHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * @param Shipment $subject
     * @return Shipment
     */
    public function afterRegister(Shipment $subject)
    {
        if ($this->scopeConfig->getValue('unific/connector/enabled') == 1) {
            $this->orderDataHelper->setShipment($subject);
            //we need to postpone sending webhook to after save so we have created_at and updated_at fields
            $this->processAfterSave = true;
        }

        return $subject;
    }

    /**
     * @param Shipment $subject
     * @return Shipment
     */
    public function afterSave(Shipment $subject)
    {
        if ($this->processAfterSave && $this->scopeConfig->getValue('unific/connector/enabled') == 1) {
            $this->emulateStore($subject->getStoreId());
            $this->processWebhook(
                $this->orderDataHelper->getOrderInfo(),
                $this->scopeConfig->getValue('unific/webhook/order_endpoint'),
                Settings::PRIORITY_ORDER,
                'order/ship'
            );
            $this->stopEmulation();
        }

        return $subject;
    }
}
