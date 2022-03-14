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
namespace FME\Mediaappearance\Model\Config\Productmedia;

class Mediaplayertype implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        //dark, light - thumb loader type
        return [
        ['value' => 'gridb', 'label' => __('Grid Bottom')],
        ['value' => 'gridl', 'label' => __('Grid Left')],
        ['value' => 'gridt', 'label' => __('Grid Top')],
        ['value' => 'gridr', 'label' => __('Grid Right')],
        ['value' => 'stripeb', 'label' => __('Compact Bottom')],
        ['value' => 'stripel', 'label' => __('Compact Left')],
        ['value' => 'stripet', 'label' => __('Compact Top')],
        ['value' => 'striper', 'label' => __('Compact Right')]];
    }
}
