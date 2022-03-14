<?php

namespace Meetanshi\Partialpro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Applymethod implements ArrayInterface
{
    public function toOptionArray()
    {
        $methods = ['1' => ['label' => 'All Products', 'value' => 1],
            '0' => ['label' => 'Selected Products', 'value' => 0],
            '2' => ['label' => 'Whole Cart', 'value' => 2]
        ];

        return $methods;
    }
}
