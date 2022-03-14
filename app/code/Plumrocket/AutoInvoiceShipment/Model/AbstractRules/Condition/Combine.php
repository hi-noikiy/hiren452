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
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition;

use Magento\Rule\Model\Condition\Context;

/**
 * Class Combine
 *
 * @method $this setType($type)
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var Order
     */
    protected $conditionOrder;
    /**
     * @var BillingAddress
     */
    protected $conditionBillingAddress;
    /**
     * @var ShippingAddress
     */
    protected $conditionShippingAddress;

    /**
     * @param Context         $context
     * @param Order           $conditionOrder
     * @param BillingAddress  $conditionBillingAddress
     * @param ShippingAddress $conditionShippingAddress
     * @param array           $data
     */
    public function __construct(
        Context $context,
        Order $conditionOrder,
        BillingAddress $conditionBillingAddress,
        ShippingAddress $conditionShippingAddress,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setType('Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\Combine');
        $this->conditionOrder           = $conditionOrder;
        $this->conditionBillingAddress  = $conditionBillingAddress;
        $this->conditionShippingAddress = $conditionShippingAddress;
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $orderAttributes = $this->conditionOrder->loadAttributeOptions()->getAttributeOption();
        $order = [];
        foreach ($orderAttributes as $code => $label) {
            $order[] = [
                'value' => 'Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\Order|' . $code,
                'label' => $label,
            ];
        }

        $billingAddressAttributes = $this->conditionBillingAddress->loadAttributeOptions()->getAttributeOption();
        $billingAddress = [];
        foreach ($billingAddressAttributes as $code => $label) {
            $billingAddress[] = [
                'value' => 'Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\BillingAddress|' . $code,
                'label' => $label
            ];
        }

        $shippingAddressAttributes = $this->conditionShippingAddress->loadAttributeOptions()->getAttributeOption();
        $shippingAddress = [];
        foreach ($shippingAddressAttributes as $code => $label) {
            $shippingAddress[] = [
                'value' => 'Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\ShippingAddress|' . $code,
                'label' => $label
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => '\Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\ItemFound',
                    'label' => __('Order Item Attribute')
                ],
                [
                    'value' => $order,
                    'label' => __('Order Attribute')
                ],
                [
                    'value' => $billingAddress,
                    'label' => __('Order Billing Address Attribute')
                ],
                [
                    'value' => $shippingAddress,
                    'label' => __('Order Shipping Address Attribute')
                ],
            ]
        );

        return $conditions;
    }
}
