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
 * @package     Plumrocket Search Autocomplete & Suggest
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Search\Block\Adminhtml\System\Config\Form;

class Attributes extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attributeFactory;

    /**
     * @var int
     */
    private $attributeElement;

    /**
     * Attributes constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                                  $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory
     * @param array                                                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory,
        array $data = []
    ) {
        $this->attributeFactory = $attributeFactory;

        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Plumrocket_Search::system/config/attributes.phtml');

        return $this;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->attributeElement = $element;

        return $this->toHtml();
    }

    /**
     * @return mixed
     */
    public function getAttributes($forBackend = false)
    {
        $allSearchableAttr = $this->getCollection()
            ->addFieldToFilter('additional_table.is_searchable', ['eq' => 0])
            ->setOrder('main_table.frontend_label', 'asc')
            ->setOrder('main_table.attribute_code', 'asc');

        if ($forBackend) {
            $attributes = $this->getAttributesSearchable();
            $ids = [];

            foreach ($attributes as $attribute) {
                $ids[] = $attribute->getAttributeCode();
            }

            if (count($ids) > 0) {
                $allSearchableAttr->addFieldToFilter('attribute_code', ['nin' => $ids]);
            }
        }

        return $allSearchableAttr;
    }

    /**
     * @return array
     */
    public function getAttributesSearchable()
    {
        $default = [
            'name'                   => 1,
            'short_description'      => 2,
            'description'            => 3
        ];

        $collection = $this->getCollection()
            ->addIsSearchableFilter()
            ->addFieldToFilter('psearch_priority', ['neq' => 0])
            ->setOrder('psearch_priority','ASC');

        if (! (bool)$collection->getSize()) {
            $collection = $this->getCollection()
                ->addIsSearchableFilter()
                ->addFieldToFilter('attribute_code', ['in' => [array_keys($default)]]);
        }

        $items = [];
        $i = 1;

        foreach ($collection as $item) {
            if (! $item->getPsearchPriority()) {
                $priority = isset($default[$item->getAttributeCode()]) ?
                    $default[$item->getAttributeCode()] : $i;

                $item->setPsearchPriority($priority);
            }

            $items[$item->getPsearchPriority()] = $item;
            $i++;
        }

        ksort($items);

        return $items;
    }

    /**
     * @param $attribute
     * @return string
     */
    public function prepareLabel($attribute)
    {
        if (! $label = $attribute->getFrontendLabel()) {
            $label = ucwords(str_replace('_', ' ', $attribute->getName()));
        }

        return $label;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    protected function getCollection()
    {
        return $this->attributeFactory->create()->addVisibleFilter();
    }

    /**
     * @return int
     */
    public function getAttributeElementId()
    {
        return $this->attributeElement->getId();
    }

    /**
     * @return mixed
     */
    public function getAttributeElementLabel()
    {
        return $this->attributeElement->getLabel();
    }

    /**
     * @param $orderString
     * @return bool|string
     */
    public function prepareAttributeOrder($orderString)
    {
        return json_encode($orderString);
    }
}
