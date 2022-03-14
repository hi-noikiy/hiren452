<?php

namespace Meetanshi\Partialpro\Model\Config\Product;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class CalculationDownPayment extends AbstractSource
{
    protected $optionFactory;

    public function getAllOptions()
    {
        $this->_options = [
            ['label' => 'Select Option', 'value' => ''],
            ['label' => 'Fixed Amount', 'value' => '1'],
            ['label' => 'Percentage Of Product Price', 'value' => '2']
        ];
        return $this->_options;
    }
}
