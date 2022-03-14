<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Mediaappearance
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Mediaappearance\Model\Config\Thumb;

class Opacity implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return [['value' => '0.1', 'label' => __('0.1')],
        ['value' => '0.2', 'label' => __('0.2')],
        ['value' => '0.3', 'label' => __('0.3')],
        ['value' => '0.4', 'label' => __('0.4')],
        ['value' => '0.5', 'label' => __('0.5')],
        ['value' => '0.6', 'label' => __('0.6')],
        ['value' => '0.7', 'label' => __('0.7')],
        ['value' => '0.8', 'label' => __('0.8')],
        ['value' => '0.9', 'label' => __('0.9')] ];
    }
}
