<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
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

namespace Plumrocket\Search\Helper;

class Search extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var SEARCH TERMS
     */
    const SEARCH_TERMS = 'search_term';

    /**
     * @var SEARCH MODULE NAME
     */
    const SEARCH_MODULE_NAME = 'catalogsearch';

    /**
     * CONDITION OR
     */
    const CONDITION_OR = 'or';

    /**
     * CONDITION AND
     */
    const CONDITION_AND = 'and';

    /**
     * CONDITION LIKE
     */
    const CONDITION_LIKE = 'like';

    /**
     * SORTED KEY
     */
    const SORTED_IDS_KEY = 'pr_search_sorted_ids';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attributeFactory;

    /**
     * @var
     */
    private $currentSearchTerm;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var string
     */
    private $currentModuleName;

    /**
     * Search constructor.
     *
     * @param \Magento\Framework\App\Helper\Context                                    $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory
     * @param Data                                                                     $dataHelper
     * @param \Magento\Framework\Registry                                              $registry
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory,
        \Plumrocket\Search\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);

        $this->currentModuleName = $context->getRequest()->getModuleName();
        $this->registry = $registry;
        $this->dataHelper = $dataHelper;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @param bool $isSearchable
     * @return array
     */
    public function getAttributes($isSearchable = false)
    {
        $attributes = $this->attributeFactory->create()
            ->addVisibleFilter()
            ->addIsSearchableFilter();

        if (! $isSearchable) {
            $attributes->setOrder('main_table.frontend_label', 'asc')
                ->setOrder('main_table.attribute_code', 'asc');
        } else {
            $attributes->addFieldToFilter('psearch_priority', ['neq' => 0])
                ->setOrder('psearch_priority','ASC');
        }

        $attributesArray = $attributes->getColumnValues('attribute_code');

        if (count($attributesArray) < 1 && $isSearchable) {
            return [
                'name',
                'short_description',
                'description'
            ];
        }

        return $attributesArray;
    }

    /**
     * @param $inputCollection
     * @return array
     */
    public function sortCollection($inputCollection)
    {
        $sortedIds = [];
        $this->currentSearchTerm = $this->dataHelper->getQueryText();

        foreach ($this->getAttributes(true) as $attribute) {
            $collection = clone $inputCollection;

            if ($this->likeSeparator() === self::CONDITION_OR) {
                $conditions = [];
                $terms = explode(' ', $this->currentSearchTerm);
                foreach ($terms as $term) {
                    $conditions[] = ['attribute' => $attribute, self::CONDITION_LIKE => '%' . trim($term) . '%'];
                }
                $filteredCollection = $collection->addAttributeToFilter($conditions);
            } else if ($this->likeSeparator() === self::CONDITION_AND) {
                $filteredCollection = $collection->addAttributeToFilter(
                    $attribute,
                    [self::CONDITION_LIKE => '%' . $this->currentSearchTerm . '%']
                );
            }

            foreach ($filteredCollection->getAllIds() as $key => $value) {
                if (! in_array($value, $sortedIds)) {
                    $sortedIds[] = $value;
                }
            }
        }

        if (count($sortedIds) > 0 && ! $this->registry->registry(self::SORTED_IDS_KEY)) {
            $this->registry->register(self::SORTED_IDS_KEY, $sortedIds);
        }

        return $sortedIds;
    }

    /**
     * @return bool
     */
    public function moduleEnabled()
    {
        return $this->dataHelper->moduleEnabled();
    }

    /**
     * @return bool
     */
    public function likeSeparator()
    {
        return $this->dataHelper->likeSeparator();
    }

    /**
     * @return bool
     */
    public function isCatalogsearchPage()
    {
        return strpos($this->currentModuleName, self::SEARCH_MODULE_NAME) !== false;
    }

    /**
     * @return mixed
     */
    public function getSortedIds()
    {
        return $this->registry->registry(self::SORTED_IDS_KEY);
    }

    /**
     * @return bool
     */
    public function isAjaxRequest()
    {
        return (bool)$this->registry->registry("psearch_isajax");
    }

    /**
     * @return bool
     */
    public function allowedLogic()
    {
        $prFilterEnabled = $this->_moduleManager->isOutputEnabled('Plumrocket_ProductFilter');
        $ajaxRequest = $this->isAjaxRequest();

        if ($ajaxRequest) {
            return true;
        }

        if ($prFilterEnabled) {
            return false;
        }

        return true;
    }
}