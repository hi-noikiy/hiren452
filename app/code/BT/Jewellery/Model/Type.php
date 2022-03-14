<?php
namespace BT\Jewellery\Model;

use Magento\Framework\Data\OptionSourceInterface;


class Type implements OptionSourceInterface
{
    /**
    * Get options
    *
    * @return array
    */
    public function toOptionArray()
    {
        $options[] = [
            'label' => 'Diamond',
            'value' => 'diamond',
        ];
        $options[] = [
            'label' => 'Gemstone',
            'value' => 'gemstone',
        ];
        return $options;
    }
}
