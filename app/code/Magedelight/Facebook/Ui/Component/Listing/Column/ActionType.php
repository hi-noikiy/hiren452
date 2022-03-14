<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Facebook
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
namespace Magedelight\Facebook\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Magedelight\Facebook\Model\Cronhistory;

/**
 * Class Options
 */
class ActionType implements OptionSourceInterface
{

    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [Cronhistory::MANUAL => Cronhistory::MANUAl_LABEL,
                        Cronhistory::CRON => Cronhistory::CRON_LABEL
                      ];
        $res = [];
        if ($this->options === null)
        {
            foreach ($optionArray as $optionid => $optionValue) {
                $additional['value'] = $optionid;
                $additional['label'] = $optionValue;
                $res[] = $additional;
            }
            $this->options = $res;
           return $this->options;
        }
        return $this->options;
    }
}

