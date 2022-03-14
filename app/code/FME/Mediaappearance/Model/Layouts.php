<?php

namespace FME\Productattachments\Model\System;

class Layouts extends \Magento\Framework\ObjectManager\ObjectManager
{


   /**
    * toOptionArray
    * @return string
    */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Empty'),
                'value' => 'empty'
            ],
            [
                'label' => __('1 column'),
                'value' => '1column'
            ],
            [
                'label' => __('2 columns with left bar'),
                'value' => '2columns-left'
            ],
            [
                'label' => __('2 column with right bar'),
                'value' => '2columns-right'
            ],
            [
                'label' => __('3 columns'),
                'value' => '3columns'
            ]
        ];
    }

    /**
     *
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
