<?php

namespace Meetanshi\Inquiry\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Fields implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => 'Tax/VAT Number'],
            ['value' => '2', 'label' => 'Address'],
            ['value' => '3', 'label' => 'Website'],
            ['value' => '4', 'label' => 'Date Time'],
            ['value' => '5', 'label' => 'Upload Files'],
            ['value' => '6', 'label' => 'Extra Field 1'],
            ['value' => '7', 'label' => 'Extra Field 2'],
            ['value' => '8', 'label' => 'Extra Field 3'],
        ];
    }
}
