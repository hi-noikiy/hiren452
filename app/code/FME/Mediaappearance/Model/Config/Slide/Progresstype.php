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

class Progresstype implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
     //pie, pie2, bar
        return [['value' => 'pie', 'label' => __('Pie 1')],
        ['value' => 'pie2', 'label' => __('Pie 2')],
        ['value' => 'bar', 'label' => __('Bar')]
        ];
    }
}
