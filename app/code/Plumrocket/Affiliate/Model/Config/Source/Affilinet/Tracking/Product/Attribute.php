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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Product;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;

/**
 * Integration status options.
 */
class Attribute implements \Magento\Framework\Option\ArrayInterface
{
    const MAX_COUNT = 5;

    /**
     * Attribute collection factory
     *
     * @var AttributeCollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * Collection of product attributes
     * @var nulll | array
     */
    protected $_options;

    /**
     * Construct method
     * @param AttributeCollectionFactory $attributeCollectionFactory
     */
    public function __construct(AttributeCollectionFactory $attributeCollectionFactory)
    {
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * Retrieve platforms options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (null === $this->_options) {
            /* Load collection */
            $collection = $this->_attributeCollectionFactory->create()
                ->addFieldToFilter('frontend_input', array('nin' => array('media_image', 'hidden', 'gallery')))
                ->addVisibleFilter();

            $collection->getSelect()->order(array('frontend_label ASC', 'attribute_code ASC'));

            foreach ($collection as $item) {
                $label = $item->getData('frontend_label');
                $value = $item->getData('attribute_code');

                $this->_options[] = [
                    'label' => $label ? $label : $value,
                    'value' => $value,
                ];
            }

            array_unshift($this->_options, [
                'label' => __('-- Please Select --'),
                'value' => '',
            ]);

            return $this->_options;
        }

        return $this->_options;
    }
}
