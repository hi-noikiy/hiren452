<?php
namespace BT\News\Model;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Kitchen365\news\Model
 */
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
