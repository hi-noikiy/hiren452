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
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Shipping\Model\Config\Source\Allmethods as ShippingAllMethods;
use Magento\Payment\Model\Config\Source\Allmethods as PaymentAllMethods;

/**
 * Class Order
 *
 * @method setAttributeOption($attributes)
 * @method $this setType($type)
 *
 * @method array  getAttributeOption()
 * @method string getAttribute()
 */
class Order extends AbstractCondition
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $connection;
    /**
     * @var ShippingAllMethods
     */
    protected $shippingAllMethods;
    /**
     * @var PaymentAllMethods
     */
    protected $paymentAllMethods;

    /**
     * @param Context            $context
     * @param ShippingAllMethods $shippingAllMethods
     * @param PaymentAllMethods  $paymentAllMethods
     * @param array              $data
     */
    public function __construct(
        Context $context,
        ShippingAllMethods $shippingAllMethods,
        PaymentAllmethods $paymentAllMethods,
        array $data = []
    ) {
        $this->setType('Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\Order');
        parent::__construct($context, $data);
        $this->shippingAllMethods   = $shippingAllMethods;
        $this->paymentAllMethods    = $paymentAllMethods;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $sourceAttributes = [
            'payment_method',
            'shipping_method',
            'shipping_amount',
            'subtotal',
            'grand_total',
            'is_virtual',
        ];

        $attributes = [];
        foreach ($sourceAttributes as $code) {
            $attributes[$code] = __('Order') . ' ' . __(ucwords(str_replace('_', ' ', $code)));
        }

        $this->setAttributeOption($attributes);
        return $this;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method':
            case 'payment_method':
            case 'is_virtual':
                return 'select';
        }
        return 'string';
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method':
            case 'payment_method':
            case 'is_virtual':
                return 'select';
        }
        return 'text';
    }

    /**
     * Get value select options
     *
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'shipping_method':
                    $options = $this->shippingAllMethods->toOptionArray();
                    break;

                case 'payment_method':
                    $options = $this->paymentAllMethods->toOptionArray();
                    break;

                case 'is_virtual':
                    $options = [
                        ['value' => 0, 'label' => __('No')],
                        ['value' => 1, 'label' => __('Yes')]
                    ];
                    break;

                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * Set payment method for validate
     *
     * @param \Magento\Framework\Model\AbstractModel | \Magento\Sales\Model\Order $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        if ($this->getAttribute() == 'payment_method') {
            $model->setData($this->getAttribute(), $model->getPayment()->getMethod());
        }
        return parent::validate($model);
    }
}
