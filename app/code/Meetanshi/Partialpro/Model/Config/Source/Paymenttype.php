<?php

namespace Meetanshi\Partialpro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Paymenttype implements ArrayInterface
{
    public function toOptionArray()
    {
        $methods = ['1' => ['label' => 'Fixed Installment Plan', 'value' => 1],
            '2' => ['label' => 'Flexy Layaway Plan', 'value' => 2]
        ];
        return $methods;
    }
}
