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
namespace FME\Mediaappearance\Model\Config\Grid;

class Gridalign1 implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        //borders, grid, middle
        return [['value' => 'borders', 'label' => __('Borders')],
        ['value' => 'grid', 'label' => __('Grid')],
        ['value' => 'center', 'label' => __('Center')] ];
    }
}
