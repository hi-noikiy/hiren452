<?php

namespace Meetanshi\Partialpro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ChargeShippingTex implements ArrayInterface
{
    public function toOptionArray()
    {
        $methods = ['0' => ['label' => 'On Down Payment', 'value' => 0],
            '1' => ['label' => 'On All Installments', 'value' => 1]
        ];

        return $methods;
    }
}
