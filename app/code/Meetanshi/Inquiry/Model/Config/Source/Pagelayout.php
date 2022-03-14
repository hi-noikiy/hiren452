<?php

namespace Meetanshi\Inquiry\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Pagelayout implements ArrayInterface{
    public function toOptionArray(){
        return[
            ['value' => 'empty', 'label' => 'Empty'],
            ['value' => '1column', 'label' => '1 column'],
            ['value' => '2columns-left', 'label' => '2 column with left bar'],
            ['value' => '2columns-right', 'label' => '2 column with right bar'],
            ['value' => '3columns', 'label' => '3 column'],
        ];
    }
}