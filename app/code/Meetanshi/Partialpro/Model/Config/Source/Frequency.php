<?php

namespace Meetanshi\Partialpro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Frequency implements ArrayInterface
{
    public function toOptionArray()
    {
        $methods = ['0' => ['label' => 'Based on Days', 'value' => 0],
            '1' => ['label' => 'Weekly', 'value' => 1],
            '2' => ['label' => 'Monthly', 'value' => 2],
            '3' => ['label' => 'Quarterly', 'value' => 3]
        ];

        return $methods;
    }
}
