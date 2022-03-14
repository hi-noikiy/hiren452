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
namespace FME\Mediaappearance\Model;

class Playvideo extends \Magento\Framework\ObjectManager\ObjectManager
{


    /**
     * toOptionArray
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Image Box'),
                'value' => '1'
            ],
            [
                'label' => __('Pop Up'),
                'value' => '2'
            ]
        ];
    }


    /**
     * __construct
     * @param \Magento\Framework\ObjectManagerInterface         $objectManager
     * @param \Magento\Framework\ObjectManager\FactoryInterface $factory
     * @param \Magento\Framework\ObjectManager\ConfigInterface  $config
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\ObjectManager\FactoryInterface $factory,
        \Magento\Framework\ObjectManager\ConfigInterface $config
    ) {
        
            parent::__construct($factory, $config);
            $this->_objectManager = $objectManager;
    }
}
