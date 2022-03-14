<?php

namespace Meetanshi\Inquiry\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Url implements ArrayInterface{
    public function toOptionArray(){
        return[
            ['value' => 'empty', 'label' => 'Top Link'],
            ['value' => '1column', 'label' => 'Footer'],
        ];
    }
}