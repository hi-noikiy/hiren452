<?php
namespace BT\Jewellery\Model;

use Magento\Framework\Data\OptionSourceInterface;


class Status implements OptionSourceInterface
{
    /**
    * Get options
    *
    * @return array
    */
    public function toOptionArray()
    {
        $options[] = [
            'label' => 'Enable',
            'value' => '1',
        ];
        $options[] = [
            'label' => 'Disable',
            'value' => '0',
        ];
        return $options;
    }
}
