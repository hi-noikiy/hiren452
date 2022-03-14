<?php

namespace Meetanshi\Partialpro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class InstallmentFeeApply implements ArrayInterface
{
    public function toOptionArray()
    {
        $methods = ['0' => ['label' => 'No', 'value' => 0],
            '1' => ['label' => 'On First Installment', 'value' => 1],
            '2' => ['label' => 'On All Installments', 'value' => 2]
        ];

        return $methods;
    }
}
