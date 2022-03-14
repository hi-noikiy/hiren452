<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Values;

class TradedoublerPixel
{

    /**
     * Options
     * @var array
     */
    protected $_options = null;

    /**
     * Options getter
     *
     * @return Array
     */
    public function toOptionArray()
    {
        return $this->_getOptions();
    }

    /**
     * Get options in "key-value" format
     *
     * @return Array
     */
    public function toArray()
    {
        $options = [];
        foreach ($this->_getOptions() as $option) {
            $options[ $option['value'] ] = $option['label'];
        }

        return $options;
    }

    /**
     * Get options
     * @return Array 
     */
    protected function _getOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => 0, 'label' => __('Disable') ],
                ['value' => 1, 'label' => __('Enable (Sale Tracking Pixel)') ],
                ['value' => 2, 'label' => __('Enable (PLT, Product Level Tracking)') ],
            ];
        }

        return $this->_options;
    }
}
