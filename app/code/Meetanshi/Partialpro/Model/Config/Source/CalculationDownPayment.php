<?php

namespace Meetanshi\Partialpro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CalculationDownPayment implements ArrayInterface
{
    public function toOptionArray()
    {
        $methods = ['1' => ['label' => 'Fixed Amount', 'value' => 1],
            '2' => ['label' => 'Percentage Of Product Price', 'value' => 2]
        ];

        return $methods;
    }
}
