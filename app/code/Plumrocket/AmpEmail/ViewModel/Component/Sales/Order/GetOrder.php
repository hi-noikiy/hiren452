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

namespace Plumrocket\AmpEmail\ViewModel\Component\Sales\Order;

class GetOrder
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * GetOrder constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \Magento\Framework\DataObject $dataObject
     * @return bool|\Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order
     */
    public function execute($dataObject)
    {
        $orderId = $dataObject->getOrderId() ?: $dataObject->getEmailTemplateVars('order_id');
        $order = $dataObject->getData('order') ?: $dataObject->getEmailTemplateVars('order');

        switch (true) {
            case $order:
                if ($order instanceof \Magento\Sales\Api\Data\OrderInterface || ! is_numeric($order)) {
                    break;
                }
                $orderId = (int) $order; // no break
            case $orderId:
                try {
                    $order = $this->orderRepository->get($orderId);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $order = false;
                }
                break;
            default:
                $order = false;
        }

        return $order;
    }
}
