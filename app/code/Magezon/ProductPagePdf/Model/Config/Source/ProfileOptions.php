<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductPagePdf\Model\Config\Source;

class ProfileOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magezon\ProductPagePdf\Model\ResourceModel\Profile\CollectionFactory
     */
    protected $profileCollectionFactory;

    /**
     * @param \Magezon\ProductPagePdf\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory
     */
    public function __construct(
        \Magezon\ProductPagePdf\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory
    ) {
        $this->profileCollectionFactory = $profileCollectionFactory;
    }
    
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = [
            'label' => __('Select a profile'),
            'value' => 0
        ];
        $collection = $this->profileCollectionFactory->create();

        foreach ($collection as $profile) {
            $options[] = [
                'label' => $profile->getName(),
                'value' => $profile->getId()
            ];
        }
        return $options;
    }
}
