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
use Magento\Directory\Model\Config\Source\Country;
use Magento\Directory\Model\Config\Source\Allregion;

use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\AddressFactory;

class ShippingAddress extends AbstractAddress
{
    /**
     * @param \Magento\Rule\Model\Condition\Context            $context
     * @param \Magento\Directory\Model\Config\Source\Country   $directoryCountry
     * @param \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion
     * @param \Magento\Sales\Model\Order\AddressFactory        $address
     * @param array                                            $data
     */
    public function __construct(
        Context $context,
        Country $directoryCountry,
        Allregion $directoryAllregion,
        AddressFactory $address,
        array $data = []
    ) {
        parent::__construct($context, $directoryCountry, $directoryAllregion, $address, $data);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        parent::loadAttributeOptions();
        $attributes = [];
        foreach ($this->getAttributeOption() as $code => $label) {
            $attributes[$code] = __('Shipping Address') . ' ' . $label;
        }

        $this->setAttributeOption($attributes);
        return $this;
    }

    /**
     * Validate Address Rule Condition
     *
     * @param  AbstractModel|\Magento\Sales\Model\Order $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        $address = $model;
        if (!$address instanceof Address) {
            $address = $model->getShippingAddress();
        }

        return parent::validate($address);
    }
}
