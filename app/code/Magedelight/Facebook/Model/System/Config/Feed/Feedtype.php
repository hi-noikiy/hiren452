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

namespace Magedelight\Facebook\Model\System\Config\Feed;

class Feedtype implements \Magento\Framework\Option\ArrayInterface
{

    const FeedXML = 'xml';
    const FeedCSV = 'csv';
    /**
     * Return feed type.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $feedtype = [
            ['value' => self::FeedCSV, 'label' => __('CSV')],
            ['value' => self::FeedXML, 'label' => __('XML')]
        ];
        return $feedtype;
    }
}

