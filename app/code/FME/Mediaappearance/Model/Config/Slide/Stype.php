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
namespace FME\Mediaappearance\Model\Config\Slide;

class Stype implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
      //fade, slide
        return [['value' => '1', 'label' => __('1')],
                ['value' => '2', 'label' => __('2')],
                ['value' => '3', 'label' => __('3')],
                ['value' => '4', 'label' => __('4')],
                ['value' => '5', 'label' => __('5')],
                ['value' => '6', 'label' => __('6')],
                ['value' => '7', 'label' => __('7')]
        ];
    }
}
